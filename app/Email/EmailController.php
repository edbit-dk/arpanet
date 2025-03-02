<?php

namespace App\Email;

use App\AppController;

use App\Email\EmailService as Mail;

class EmailController extends AppController
{
    public function mail()
    {
        return Mail::handle(parse_request('data'));
    }

}