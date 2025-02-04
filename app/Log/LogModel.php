<?php

namespace App\Log;

use Illuminate\Database\Eloquent\Model;

class LogModel extends Model
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