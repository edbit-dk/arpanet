<?php

namespace App\Help;

use Lib\Cache;

use App\Help\HelpModel as Help;

class HelpService extends Help
{
    
    public static function paginate($type, $limit, $offset)
    {
        return Cache::remember($type.$limit.$offset, fn() => Help::orderBy('id', 'asc')
        ->where($type,1)
        ->limit($limit)
        ->offset($offset)->get());
    }

    public static function search($data, $type)
    {
        return Cache::remember($type.$data, fn() => Help::where($type,1)
        ->where('cmd','LIKE', '%' . $data . '%')->first());
    }

    public static function count($type)
    {
        return Cache::remember("$type.count", fn() => Help::where($type,1)->count());
    }

}
