<?php

namespace App\Email;

use App\AppModel;

use App\Mission\MissionModel as Mission;
use App\User\UserModel as User;

class EmailModel extends AppModel 
{
    protected $table = 'emails';

    protected $guarded = [];

    protected $maps = [
        'user_id' => 'user_id',
        'sender' => 'sender',
        'recipient' => 'recipient',
        'subject' => 'subject',
        'message' => 'message',
        'is_read' => 'is_read',
    ];

    public function missions() 
    {
        return $this->hasMany(Mission::class, 'email_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}