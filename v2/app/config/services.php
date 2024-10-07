<?php

// Config Auth
$c['auth'] = function ($c) {
    return new App\Services\Auth;
};

$c['mainframe'] = function ($c) {
    return new App\Services\Mainframe;
};