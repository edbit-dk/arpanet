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

        $command = $data[0];
        if(isset($data[1])) {
            unset($data[0]);
            $args = $data;
            $options = explode('<', trim(request()->get('data')));
        } else {
            $args = '';
        }

       switch ($command) {
        case ($command == '-l' || $command == 'list'):
            $this->list();
            break;

        case ($command == '-s' || $command == 'send'):
            $this->send($options);
            break;

        case ($command == '-r' || $command == 'read'):
            $this->read($args);
            break;

        case ($command == '-d' || $command == 'delete'):
            $this->delete($args);
            break;
        
        default:
            # code...
            break;
       }
      
    }

    public function list()
    {
        $id = 1;
        $emails = Email::where('recipient', User::auth())->orWhere('recipient', User::username())->get();

        foreach ($emails as $email) {
            $id++;
            echo "$email->id. [$email->subject]\n";
        }
            
    }

    public function send($data)
    {
        if(empty($data)) {
            $id = 1;
            $emails = Email::where('sender', User::auth())->orWhere('sender', User::username())->get();

            foreach ($emails as $email) {
                $id++;
                echo "$email->id. [$email->subject]\n";
            }

            exit;
        }

        $options = explode(' ',trim($data[0]));
        $body = trim($data[1]);
        $subject = $options[1];
        $sender = User::username();
        $to = $options[2];

        Email::create([
            'sender' => $sender,
            'recipient' => $to,
            'subject' => $subject,
            'body' => $body
        ]);

        echo 'Email Sent.';
    }

    public function read($data)
    {
        $email = Email::where('id', $data)->orWhere('subject', $data)->first();
        
        if(!empty($email)) {
            echo $email->body;
        } else {
            echo "ERROR: No Email.";
        }

    }

    public function delete($data)
    {
        echo 'delete ' . $data;
    }

}