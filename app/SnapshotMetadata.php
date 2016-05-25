<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SnapshotMetadata extends Model
{
    //
    protected $table = 'snapshot_metadata';

    protected $fillable = ['id', 'ostypeid'];
}
