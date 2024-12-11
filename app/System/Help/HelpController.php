<?php

namespace App\System\Help;

use Lib\Controller;
use Lib\Session;

use App\User\UserService as User;
use App\Host\HostService as Host;

use App\System\Help\HelpModel as Help;

class HelpController extends Controller
{

    private $commands;
    private $response;
    private $data;

    private function cmd($type)
    {
        $this->data = parse_request('data');

        $this->commands = Help::where($type,1)->get();

        if($this->data[0] == 'auto') {
            foreach ($this->commands as $item) {
                $this->response[] = $item->cmd;
            }

            echo json_encode($this->response);
            exit;
        }

        if(empty($this->data[0])) {

            foreach ($this->commands as $item) {

                $cmd = strtoupper($item->cmd);
                $input = $item->input;
                $info = $item->info;

                echo "  $cmd $input\n";
            }

            exit;

        } 

        if(!empty($this->data[0])) {

            $help = Help::where($type,1)->where('cmd','LIKE', '%' . $this->data[0] . '%')->first();

            if(empty($help)) {
                echo 'ERROR: Unknown Command.';
                exit;
            }

            $cmd = strtoupper($help->cmd);
            $input = $help->input;
            $info = $help->info;
            
            echo <<< EOT
            $cmd $input
                $info
            EOT;
            exit;
        }
    }

    public function visitor()
    {
       return $this->cmd('is_visitor');
    }

    public function guest()
    {
        return $this->cmd('is_guest');
    }

    public function host()
    {
        return $this->cmd('is_host');
    }

    public function user()
    {
        return $this->cmd('is_user');
    }

}