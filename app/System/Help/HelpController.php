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

        $paginate = paginate($this->data[0], Help::where($type,1)->count(), 10);
        $page = $paginate['page'];
        $limit = $paginate['limit'];
        $offset = $paginate['offset'];
        $total = $paginate['total'];

        $this->commands = Help::orderBy('id', 'asc')
                        ->where($type,1)
                        ->limit($limit)
                        ->offset($offset)->get();

        if(empty($this->data[0]) || is_numeric($this->data[0])) {

            foreach ($this->commands as $item) {

                $cmd = strtoupper($item->cmd);
                $input = $item->input;
                $info = $item->info;

                echo "$cmd $input\n";
            }
            echo "\nhelp {$page}/{$total}\n";

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