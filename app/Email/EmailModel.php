<?php

namespace App\Email;

use Illuminate\Database\Eloquent\Model;

use App\Mission\MissionModel as Mission;
use App\User\UserModel as User;

class EmailModel extends Model 
{
    protected $table = 'emails';
    protected $fillable = ['user_id', 'sender', 'recipient', 'subject', 'message', 'is_read'];

    public function missions() 
    {
        return $this->hasMany(Mission::class, 'email_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}