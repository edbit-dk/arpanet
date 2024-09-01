<?php

class Server {

    private static $table = 'servers';
    public static $session = 'SERVER';

    public static $id = 'id';
    public static $admin_id = 'admin_id';
    public static $admin_pass = 'admin_pass';
    public static $name = 'name';
    public static $status = 'status';
    public static $location = 'location';
    public static $nodes = 'nodes';
    public static $level_id = 'level_id';
    public static $created_at = 'created_at';


    public static function get($field, $value) { 
        $db = DB::table(self::$table);

        return $db->where($field, '=', $value)->first();
    }

    public static function login() {

    }

}
