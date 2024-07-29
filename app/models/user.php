<?php

class User {

    private static $table = 'users';


    public static function db() {
        
        return DB::table(self::$table);
    }

    public static function get($field, $value) {

        $db = DB::table(self::$table);

        return $db->where($field, '=', $value)->first();
    }

    public static function create($data = []) {
        $db = DB::table(self::$table);

        return $db->insert($data);
    }

    public static function update($where, $data = []) {
        $db = DB::table(self::$table);

        $cond = explode(',', $where);

        return $db->where($cond[0],$cond[1], $cond[2])->update($data);
    }

    public static function login() {

    }

    public static function logout() {

    }


}
