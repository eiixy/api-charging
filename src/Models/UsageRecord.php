<?php

namespace Eiixy\ApiCharging\Models;

use Illuminate\Database\Eloquent\Model;

class UsageRecord extends Model
{
    //
    protected $table = 'usage_records';
    protected $guarded = [];

    protected $casts = [
        'options' => 'array'
    ];
}
