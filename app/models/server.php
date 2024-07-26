<?php

$db_server = DB::table('servers');

function server_get($field, $value) {
    global $db_server;

    return $db_server->where($field, '=', $value)->first();

}