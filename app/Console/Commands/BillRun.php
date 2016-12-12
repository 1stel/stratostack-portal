<?php namespace App\Console\Commands;

use App\DiskType;
use App\ElementCost;
use App\Package;
use App\Repositories\Eloquent\CreditCard;
use App\Repositories\UsageRepository;
use App\SiteConfig;
use App\Transaction;
use App\UsageDisk;
use App\UsageInstance;
use App\User;
use App\VmInstance;
use Illuminate\Console\Command;
use Config;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BillRun extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bill:run';
    protected $repo;
    private $pricing;
    private $taxCloud;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->repo = new UsageRepository();
        $this->taxCloud = app('App\Repositories\TaxCloudRepository');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $grandfatherPricing = SiteConfig::whereParameter('grandfatherPricing')->first()->data;
        $priceMethod = SiteConfig::whereParameter('priceMethod')->first()->data;
        $domainId = SiteConfig::whereParameter('domainId')->first()->data;

        $tcOrigin = Config::get('taxcloud.originAddress');
        $this->taxCloud->setOriginAddress($tcOrigin['address1'], $tcOrigin['address2'], $tcOrigin['city'], $tcOrigin['state'], $tcOrigin['zip']);

        if ($priceMethod == 'fixedRatio') {
            $priceData = SiteConfig::where('parameter', 'LIKE', '%Price')->get();

            foreach ($priceData as $pd) {
                $this->pricing[$pd->parameter] = $pd->data;
            }
        } else if ($priceMethod == 'elementPrice') {
            $priceData = ElementCost::all();

            foreach ($priceData as $pd) {
                $this->pricing[$pd->element]["{$pd->quantity}-{$pd->quantity_type}"] = $pd->price;
            }
        }

        // Get all of the users with a bill date of today.
        $users = User::billableToday()->chunk(100, function ($users) use ($grandfatherPricing, $priceMethod, $domainId) {
            foreach ($users as $user) {
                $acsAccountData = app('Cloudstack\CloudStackClient')->listUsers(['account' => $user->email, 'domainid' => $domainId])[0];

                $invoiceID = uniqid();

                $this->info("Working on user $user->email, account ID $acsAccountData->accountid");

                // Get usage records data for VM, disk and general.
                UsageInstance::where('accountId', '=', $acsAccountData->accountid)->chunk(100, function ($instanceUsage) {
                    // Go through the usage and make a nice array that we can invoice from
                    foreach ($instanceUsage as $record) {
                        $this->repo->addRecord($record);
                    }
                });

                UsageDisk::where('accountId', '=', $acsAccountData->accountid)->chunk(100, function ($diskUsage) {
                    foreach ($diskUsage as $record) {
                        $this->repo->addRecord($record);
                    }
                });

                // Begin pricing VMs and setting TIC on each record.
                $usage = $this->repo->all();
                print_r($usage);

                $usage['subtotal'] = 0;
                // Loop through the aggregated records and add pricing and tax information

                foreach ($usage['instance'] as $vmid => &$instance) {
                    foreach ($instance as $so_id => &$so_instance) {
                        $pkg = Package::where('cpu_number', '=', $so_instance['resources']['cpunumber'])
                            ->where('ram', '=', $so_instance['resources']['memory'])
                            ->where('disk_size', '=', $so_instance['resources']['disk_size'])
                            ->where('disk_type', '=', $so_instance['resources']['disk_type'])
                            ->first();

                        if ($pkg instanceof Package) {
                            $so_instance['tic'] = $pkg->tic;

                            // Set the price
                            $so_instance['price'] = ($pkg->price / 720) * $so_instance['usage'];
                        } else {
                            // Instance has custom parameters and should be priced by its individual elements.
                            if ($priceMethod == 'fixedRatio') {
                                $so_instance['price'] = $so_instance['resources']['cpunumber'] * ($this->pricing['corePrice'] / 720) * $so_instance['usage'];
                                $so_instance['price'] += ($so_instance['resources']['memory'] / 1024) * ($this->pricing['ramPrice'] / 720) * $so_instance['usage'];

                                $storageType = DiskType::find($so_instance['resources']['disk_type']);
                                $diskAmount = $so_instance['resources']['disk_size'] / 1024 / 1024 / 1024;
                                $so_instance['price'] += $diskAmount * ($this->pricing["{$storageType->tags}Price"] / 720) * $so_instance['usage'];
                            } else if ($priceMethod == 'elementPrice') {
                                $so_instance['price'] = $this->pricing['CPU']["{$so_instance['resources']['cpunumber']}"] * $so_instance['usage'];

                                $memoryIdentifier = $so_instance['resources']['memory'] / 1024 . "-GB";
                                $so_instance['price'] += $this->pricing['RAM']["{$memoryIdentifier}"] * $so_instance['usage'];

                                $storageType = DiskType::find($so_instance['resources']['disk_type']);
                                $diskAmount = $so_instance['resources']['disk_size'] / 1024 / 1024 / 1024;

                                $so_instance['price'] += $this->pricing[$storageType->tags][$diskAmount] * $so_instance['usage'];
                            }
                            $so_instance['tic'] = 30070; // !!REVISE!!
                        }

                        if ($grandfatherPricing == 'YES') {
                            // Check to see if we have this instance on record
                            $gfInstance = VmInstance::where('vm_instance_id', '=', $vmid)
                            ->where('cpu_number', '=', $so_instance['resources']['cpunumber'])
                            ->where('memory', '=', $so_instance['resources']['memory'])
                            ->where('disk_size', '=', $so_instance['resources']['disk_size'])
                            ->where('disk_type', '=', $so_instance['resources']['disk_type'])
                            ->first();

                            if ($gfInstance instanceof VmInstance) {
                                $so_instance['price'] = $gfInstance->rate;
                            } else {
                                $newVm = new VmInstance(['vm_instance_id' => $vmid,
                                                     'cpu_number'     => $so_instance['resources']['cpunumber'],
                                                     'memory'         => $so_instance['resources']['memory'],
                                                     'disk_size'      => $so_instance['resources']['disk_size'],
                                                     'disk_type'      => $so_instance['resources']['disk_type'],
                                                     'rate'           => $so_instance['price']]);

                                $user->instances()->save($newVm);
                            }
                        }
                            $usage['subtotal'] += $so_instance['price'];
                    }
                }

                // Grab a collection of credit cards.  We use billing address off the primary card for tax purposes.
                $cards = CreditCard::where('user_id', '=', $user->id)->orderBy('primary', 'desc')->get();
                $paymentGw = app('\App\Repositories\Contracts\PaymentRepositoryInterface');

                $primaryCard = $paymentGw->get($cards->first()->id, $user->id);

                // Compute Sales Tax
                $this->taxCloud->setDestAddress(
                    $primaryCard['address'],
                    '',
                    $primaryCard['city'],
                    $primaryCard['state'],
                    (strpos($primaryCard['zipcode'], '-') === false) ? $primaryCard['zipcode'] : substr($primaryCard['zipcode'], 0, 5),
                    ''
                );

                $usage['tax'] = $this->taxCloud->calculateSalesTax($user->id, $invoiceID, $usage);
                $usage['total'] = $usage['subtotal'] + $usage['tax'];

                // Record the uniqueID.
                $usage['invoiceNumber'] = $invoiceID;

                // Record invoice for the total amount.
                $transInvoice = new Transaction(['amount' => $usage['total'], 'note' => 'Invoice', 'invoice_number' => $invoiceID]);
                $user->transactions()->save($transInvoice);

                // Bill the user
                foreach ($cards as $card) {
                    $response = $paymentGw->charge($card->payment_profile_id, $usage['total']);

                    if ($response == false) { // Charge failed
                    // Notify the user their card failed to charge.

                        // Go on to the next one.
                        continue;
                    }

                    // Record payment and set next bill date
                    $transPayment = new Transaction(['amount' => -$usage['total'], 'note' => 'Payment']);
                    $user->transactions()->save($transPayment);
                    $user->bill_date = date('Y-m-d', strtotime("+1 month"));

                    break; // If its successful, proceed.
                }

                // Serialize the cache and save it for the user it was generated for
                $invoice = new \App\Invoice(['invoice_data' => serialize($usage)]);
                $user->invoices()->save($invoice);

                // Reset the cache for the next user
                $this->repo->clearCache();
            }
        });
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
