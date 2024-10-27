<?php

namespace App\Log;

use Illuminate\Database\Eloquent\Model;

class LogModel extends Model
{
    protected $table = 'logs';
    public $timestamps = true;
    
}