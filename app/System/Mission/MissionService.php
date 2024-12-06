<?php

namespace App\System\Mission;

class MissionService
{
    public static function dispatch($event, $data = []) {}

    public static function trigger($event, $data) {}

    public static function validate($conditions, $data) {}

    public static function activate() {}

    public static function import() {}

    public static function complete($mission_id) {}

    public static function mail($data = []) {}

    public static function reward($points) {}
}