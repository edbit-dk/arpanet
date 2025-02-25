<?php

namespace App\Email;

use App\Email\EmailModel as Email;

use App\User\UserService as User;
use App\User\UserModel as Users;

use App\Host\HostService as Host;
use App\Host\HostModel as Hosts;

class EmailService 
{
    public static function version()
    {
        echo 'Mail version 1.1 6/6/83';
    }

    public static function handle($data)
    {
        $command = $data[0];
        $options = [];
        
        if(is_numeric($command)) {
            return self::read($command);
        }

        if(isset($data[1])) {
            unset($data[0]);
            $args = $data;
            $options = explode('<', trim(request()->get('data')));
        } else {
            $args = '';
        }

       switch ($command) {
        case ($command == '-l' || $command == 'list'):
            self::list();
            break;

        case ($command == '-s' || $command == 'send'):
            self::send($options);
            break;

        case ($command == '-r' || $command == 'read'):
            self::read($args);
            break;

        case ($command == '-d' || $command == 'delete'):
            self::delete($args);
            break;

        case ($command == '-v' || $command == 'version'):
            self::version();
            break;
        
        default:
            self::list();
            break;
       }
    }

    public static function contact()
    {
        $contact = User::username();

        if(Host::data()) {
            $host = Host::hostname();
            $contact = "$contact@$host";
        }

        return $contact;
    }

    public static function list()
    {
        $id = 0;
        $unread = '';

        $emails = Email::where('recipient', self::contact());

        $count = $emails->count();
        $new = Email::where('recipient', self::contact())->where('is_read', 0)->count();
        echo "$count message(s) | $new unread \n";

        foreach ($emails->get() as $email) {
            $id++;
            $date = timestamp($email->created_at);
            if(!$email->is_read) {
                $unread = 'N';
            }
            echo ">$unread $email->id $email->sender   ($date) [$email->subject] \n";
        }
    }

    public static function unread()
    {
        $emails = Email::where('recipient', self::contact())->where('is_read', 0);
        if($emails->exists()) {
            return "You have new mail.";
        }
        return "You have no mail.";

    }

    public static function read($data, $type = 'recipient')
    {
        $contact = self::contact();

        $email = Email::where('id', $data)
        ->where($type, $contact)->first();
        
        if(!empty($email)) {

            $date = timestamp($email->created_at);
            $from = $email->sender;
            $to = $email->recipient;
        
            if(!$from) {
                $from = 'UNKNOWN';
            }

            if(!$to) {
                $to = 'UNKNOWN';
            }

            echo <<< EOT
            From: $from
            To: $to
            Date: $date
            Subject: $email->subject
            Message:
            
            $email->message
            EOT;

            Email::where('id', $data)
            ->where($type, $contact)
            ->update(['is_read' => 1]);

        } else {
            echo "ERROR: Unknown Email.";
        }

    }

    public static function send($data, $system_mail = false)
    {

        if(empty($data)) {
            $id = 1;
            $emails = Email::where('sender', self::contact());

            $count = $emails->count();
            echo "$count message(s)\n";

            foreach ($emails->get() as $email) {
                $id++;
                echo "> $email->id $email->recipient    ($email->created_at) [$email->subject] \n";
            }

            exit;
        }

        $options = explode(' ',trim($data[0]));

        if(is_numeric($options[1])) {
            return self::read($options[1], 'sender');
            exit;
        } 

        // Send email
        $message = trim($data[1]);
        $subject = $options[1];
        $sender = ($system_mail) ? $system_mail : self::contact();

        if(isEmail(trim($options[2]))) {
            $email = $options[2];
            $mail = explode('@', $email);
            $username = $mail[0];
            $host = $mail[1];
        } else {
            $username = $options[2];
        }

        if(Users::where('username', $username)->exists()) {
            
            if(Hosts::where('hostname', $host)->exists()) {
                $recipient = "$username@$host";
            } else {
                $recipient = $username;
            }

            Email::create([
                'user_id' => User::auth(),
                'sender' => $sender,
                'recipient' => $recipient,
                'subject' => $subject,
                'message' => $message
            ]);

            if(!$system_mail) {
                echo 'Email Sent.';
            }
            
        } else {
            echo 'Unknown Recipient.';
        }
    }

    public static function delete($data)
    {
        if(!empty($data[1])) {
            $email = Email::where('id', $data[1])
            ->where('user_id', User::auth())->delete();

            if(!$email) {
                echo 'Unknown Email.';
            } else {
                echo 'Email Deleted.';
            }
        } else {
            echo 'Unknown Email.';
        }
    }
}