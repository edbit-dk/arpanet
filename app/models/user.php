<?php

class User {

    private static $table = 'users';
    private static $session = 'USER';


    public static function db() {
        
        return DB::table(self::$table);
    }

    public static function session() {
        return $_SESSION[self::$session];
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

    public static function login($user) {

        $db = DB::table(self::$table);

        return $db
            ->join('levels', 'levels.id = users.level_id', 'LEFT')
            ->where('email', '=', $user['email'])
            ->where('password', '=', $user['password'])
            ->orWhere('username', '=', $user['email'])
            ->first();

    }

    public static function logout() {
        $_SESSION = array();
        session_destroy();

        return "DISCONNECTING from PoseidoNET...\n";
    }


}
