<?php

namespace App\Host\Type;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Host\HostModel as Host;

class TypeModel extends Model
{
    protected $table = 'types';

    public $timestamps = true;

    public function host(): BelongsTo   
    {
        return $this->belongsTo(Host::class);
    }
    
}