<?php

namespace App\System\Mission;

use Illuminate\Database\Eloquent\Model;

use  App\System\Email\EmailModel as Email;
use App\User\UserModel as User;

class MissionModel extends Model {
    protected $table = 'missions';
    protected $fillable = ['title', 'description', 'trigger_event', 'conditions', 'rewards', 'status', 'email_id', 'next_mission_id'];

    public function email() 
    {
        return $this->belongsTo(Email::class, 'email_id');
    }

    public function users() 
    {
        return $this->belongsToMany(User::class, 'user_missions')
                    ->withPivot('status', 'started_at', 'completed_at')
                    ->withTimestamps();
    }
}