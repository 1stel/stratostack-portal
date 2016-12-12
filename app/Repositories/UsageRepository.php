<?php
namespace App\Repositories;

use App\DiskType;
use App\UsageDisk;
use App\UsageInstance;

class UsageRepository
{

    private $_instances;
    private $_disks;
    private $_other;

    private function getInstance($instanceId, $serviceOfferingId)
    {
        // Check to see if the VM we have usage record for is in the list.
        if (isset($this->_instances[$instanceId][$serviceOfferingId])) {
            return $this->_instances[$instanceId][$serviceOfferingId];
        } else {
            return false;
        }
    }

    private function getDisk($volId)
    {
        if (isset($this->_disks[$volId])) {
            return $this->_disks[$volId];
        } else {
            return false;
        }
    }

    // addRecord
    public function addRecord($record)
    {
        // Add a record to the usage structure.

        if ($record instanceof UsageInstance) {
            $entry = $this->getInstance($record->vmInstanceId, $record->serviceOfferingId);

            if ($entry === false) {
            // Create a new entry
                $entry = ['startdate' => $record->startDate,
                          'enddate'   => $record->endDate,
                          'resources' => ['cpunumber' => $record->cpuNumber,
                                          'memory'    => $record->memory],
                          'usage'     => $record->usage,
                          'name'      => $record->vm_name];
            } else {
                // Add usage from the record we have to the existing VM entry.
                $entry['usage'] += $record->usage;

                // If the start date of the record is before our VM's start date, use the
                if ($record->startDate->lt($entry['startdate'])) {
                    $entry['startdate'] = $record->startDate;
                }

                // Same treatment for the end date.
                if ($record->endDate->gt($entry['enddate'])) {
                    $entry['enddate'] = $record->endDate;
                }
            }

            // Save the entry
            $this->_instances[$record->vmInstanceId][$record->serviceOfferingId] = $entry;
        } else if ($record instanceof UsageDisk) {
            $entry = $this->getDisk($record->volumeId);

            if ($entry === false) {
                $diskType = DiskType::whereTags($record->tags)->first();

                $entry = ['startdate' => $record->startDate,
                          'enddate'   => $record->endDate,
                          'size'      => $record->size,
                          'acs_type'  => $record->type,
                          'usage'     => $record->usage,
                          'instance'  => $record->vmInstanceId];

                if ($diskType instanceof DiskType) {
                    $entry['type'] = $diskType->id;
                }
            } else {
                $entry['usage'] += $record->usage;

                // If the start date of the record is before our VM's start date, use the
                if ($record->startDate->lt($entry['startdate'])) {
                    $entry['startdate'] = $record->startDate;
                }

                // Same treatment for the end date.
                if ($record->endDate->gt($entry['enddate'])) {
                    $entry['enddate'] = $record->endDate;
                }
            }

            $this->_disks[$record->volumeId] = $entry;
        }
    }

    public function all()
    {
        foreach ($this->_disks as $volId => $disk) {
        // Match root disks to their instances

            $id = $disk['instance'];

            if ($disk['acs_type'] != 'Root Volume' ||
                !isset($this->_instances[$id]) ||
                !is_array($this->_instances[$id])
            ) {
                continue;
            }

            foreach ($this->_instances[$id] as &$instance) {
                if ($disk['startdate'] == $instance['startdate'] && $disk['enddate'] == $instance['enddate']) {
                    $instance['resources']['disk_size'] = $disk['size'];

                    if (isset($disk['type'])) {
                        $instance['resources']['disk_type'] = $disk['type'];
                    } else {
                        $instance['resources']['disk_type'] = 0; // 0 will match nothing.
                    }

                    echo "Matched vol {$disk['instance']} to an instance.\n";

                    unset($this->_disks[$volId]);
                }
            }
        }

        // Output all usage from memory.
        return ['disk' => $this->_disks, 'instance' => $this->_instances, 'other' => $this->_other];
    }

    public function clearCache()
    {
        // Clear the internal cache of usage
    }
}
