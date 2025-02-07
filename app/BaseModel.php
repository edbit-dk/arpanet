<?php

namespace App;

use Lib\Traits\Mappable;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model 
{
    use Mappable;

}
