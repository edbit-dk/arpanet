<?php

namespace App\System\Email;

use Lib\Controller;

use App\System\Email\EmailService as Mail;

class EmailController extends Controller
{
    public function mail()
    {
        return Mail::handle(parse_request('data'));
    }

}