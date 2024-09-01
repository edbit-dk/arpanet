<?php

class User {

    private static $table = 'users';
    public static $session = 'USER';

    public static $id = 'id';
    public static $email = 'email';
    public static $username = 'username';
    public static $password = 'password';
    public static $firstname = 'firstname';
    public static $lastname = 'lastname';
    public static $fullname = 'fullname';
    public static $active = 'active';
    public static $level_id = 'level_id';
    public static $xp = 'xp';
    public static $rep = 'rep';
    public static $last_login = 'last_login';
    public static $created_at = 'created_at';
    public static $updated_at = 'updated_at';

    public static function db() {
        
        return DB::table(self::$table);
    }

    public static function session() {
        return Session::get(self::$session);
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

    public static function auth($user) {

        $db = DB::table(self::$table);

        return $db
            ->join('levels', 'levels.id = users.level_id', 'LEFT')
            ->where('email', '=', $user['email'])
            ->where('password', '=', $user['password'])
            ->first();

    }


}
