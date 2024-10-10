<?php

namespace App\Services;

class Controller 
{

    protected $c;

    public function __construct($container)
    {
        $this->c = $container;
    }

    public function __get($property) 
    {
        if($this->c->{$property}) {
            return $this->c->{$property};
        }
    }
}