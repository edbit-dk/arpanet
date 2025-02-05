<?php

namespace App;

use Lib\Traits\Mappable;
use Lib\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model 
{
    use Mappable, Cachable;
}
