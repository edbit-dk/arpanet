<?php

namespace App\System\Level;

use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    protected $table = 'levels';

    protected $fillable = [
		'user_id',
        'badge',
        'level',
        'reward'
    ];

    public $timestamps = true;
    
}