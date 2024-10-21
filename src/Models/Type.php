<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Host;

class Type extends Model
{
    public $timestamps = true;

    public function host(): BelongsTo   
    {
        return $this->belongsTo(Host::class);
    }
    
}