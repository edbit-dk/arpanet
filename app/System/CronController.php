<?php

namespace App\System;

use Lib\Controller;

use App\Host\HostModel as Hosts;

use App\User\UserService as User;
use App\Host\HostService as Host;

class CronController extends Controller
{
    public function minify()
    {
        $js = file_get_contents(BASE_PATH . '/resources/js/main.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/events.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/helpers.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/input.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/commands.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/prompts.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/terminal.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/music.js');

        $css = file_get_contents(BASE_PATH . '/resources/css/reset.css');
        $css .= file_get_contents(BASE_PATH . '/resources/css/main.css');
        $css .= file_get_contents(BASE_PATH . '/resources/css/terminal.css');

        file_put_contents(BASE_PATH . '/public/js/app.js', $js);
        file_put_contents(BASE_PATH . '/public/js/app.min.js', minify_js($js));

        file_put_contents(BASE_PATH . '/public/css/app.css', $css);
        file_put_contents(BASE_PATH . '/public/css/app.min.css', minify_css($css));

        print_r(file_get_contents(BASE_PATH . '/public/js/app.min.js'));
        print_r(file_get_contents(BASE_PATH . '/public/css/app.min.css'));
    }

    public function stats()
    {
        $date = date('H:i l, F j, Y', time());
        $users = User::count();
        $hosts = Host::count();

        $welcome = <<< EOT
        Local time is {$date}.
        There are {$users} local users. There are {$hosts} hosts on the network.
        EOT;

        Hosts::where('id', 1)->update(['welcome' => $welcome]);

    }
}