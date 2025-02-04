<?php

namespace App\Email;

use Illuminate\Database\Eloquent\Model;

use App\Mission\MissionModel as Mission;
use App\User\UserModel as User;

class EmailModel extends Model 
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