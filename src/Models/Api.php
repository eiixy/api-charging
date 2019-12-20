<?php

namespace Eiixy\ApiCharging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Api extends Model
{
    //
    protected $table = 'apis';
    protected $guarded = [];

    /**
     * @param string $column
     * @return mixed
     */
    public static function getAllFromCache($column = 'updated_at')
    {
        $last_updated_at = self::query()->max($column);
        $time = strtotime($last_updated_at);
        return Cache::remember('apis-' . $time, 1440, function () {
            return self::select('id', 'method', 'uri', 'price')->get();
        });
    }
}
