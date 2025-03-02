<?php

namespace App\Log;

use App\AppModel;

class LogModel extends AppModel
{
    protected $table = 'logs';
    public $timestamps = true;

    protected $guarded = [];

    protected $maps = [
        'id' => 'id',
        'logname' => 'logname',
        'content' => 'content',
        'host_id' => 'host_id',
        'user_id' => 'user_id',
    ];
    
}