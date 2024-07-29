<?php

class Server {

    private static $table = 'servers';

    public static function get($field, $value) { 
        $db = DB::table(self::$table);

        return $db->where($field, '=', $value)->first();
    }

}
