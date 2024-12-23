<?php

namespace App\System\Email;

use Illuminate\Database\Eloquent\Model;

use App\System\Mission\MissionModel as Mission;

class EmailModel extends Model 
{
    protected $table = 'emails';
    protected $fillable = ['sender', 'recipient', 'subject', 'body', 'timestamp', 'is_read'];

    public function missions() 
    {
        return $this->hasMany(Mission::class, 'email_id');
    }
}