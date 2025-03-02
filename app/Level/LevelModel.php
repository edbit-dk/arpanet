<?php

namespace App\Level;

use App\AppModel;

class LevelModel extends AppModel
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