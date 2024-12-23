<?php

namespace App\System\Email;

use Lib\Controller;

use App\System\Email\EmailModel as Email;

use App\User\UserService as User;

class EmailController extends Controller
{
    public function mail()
    {
       $data = parse_request('data');

       switch ($data[0]) {
        case 'l':
            $this->list();
            break;

        case 's':
            $this->send($data[1]);
            break;

        case 'r':
            $this->read($data[1]);
            break;

        case 'd':
            $this->delete($data[1]);
            break;
        
        default:
            # code...
            break;
       }
      
    }

    public function list()
    {
        $id = 1;
        $emails = Email::where('recipient', User::auth())->get();

        foreach ($emails as $email) {
            $id++;
            echo "$id $email->subject";
        }
            
    }

    public function send($data)
    {
        echo 'send ' . $data;
    }

    public function read($data)
    {
        echo 'read ' . $data;
    }

    public function delete($data)
    {
        echo 'delete ' . $data;
    }

}