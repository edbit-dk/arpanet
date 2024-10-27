<?php

namespace Custom;

class Controller 
{

    protected $c;

    public function __construct($app)
    {
        $this->c = $app->container;
    }

    public function __get($property) 
    {
        if($this->c->{$property}) {
            return $this->c->{$property};
        }
    }
}