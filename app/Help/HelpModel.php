<?php

namespace App\Help;

use App\BaseModel;

class HelpModel extends BaseModel
{
    protected $table = 'help';

    public $timestamps = true;

    protected $guarded = [];

    protected $maps = [
		'cmd' => 'cmd',
        'input' => 'input',
        'info' => 'info',
        'is_user' => 'is_user',
        'is_host' => 'is_host',
        'is_visitor' => 'is_visitor',
        'is_guest' => 'is_guest',
    ];
    
}