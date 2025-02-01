<?php

namespace Lib;

class Controller 
{

    protected $c;
    protected $request;

    public function __construct($app)
    {
        $this->c = $app->container;
        $this->request = $app->request;
    }

    public function __get($property) 
    {
        if($this->c->{$property}) {
            return $this->c->{$property};
        }
    }
}