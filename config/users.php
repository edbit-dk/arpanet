<?php

return [
    [
        'id' => 1,
        'username' => 'root', 
        'email' => 'root@teleterm.net', 
        'password' => random_pass(),
        'code' => access_code(),
        'fullname' => 'Superuser',
        'is_admin' => 0,
        'level_id' => 6,
        'xp' => 100
    ],
    [
        'id' => 2,
        'username' => 'admin', 
        'email' => 'admin@teleterm.net',
        'password' => random_pass(),
        'code' => access_code(),
        'fullname' => 'Administrator',
        'is_admin' => 1,
        'level_id' => 6,
        'xp' => 100
    ],
    [
        'id' => 3,
        'username' => 'guest', 
        'email' => 'guest@teleterm.net', 
        'password' => null,
        'code' => access_code(),
        'fullname' => 'Guest account',
        'is_admin' => 0,
        'level_id' => 1,
        'xp' => 0
    ]
];