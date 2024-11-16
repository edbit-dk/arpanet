<?php

namespace App\System\Log;

use Illuminate\Database\Eloquent\Model;

class LogModel extends Model
{
    protected $table = 'logs';
    public $timestamps = true;
    
}