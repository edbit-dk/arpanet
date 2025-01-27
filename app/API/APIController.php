<?php

namespace App\API;

use Lib\Controller;

use Illuminate\Database\Eloquent\Collection;

use App\Host\HostService as Host;
use App\User\UserService as User;
use App\Host\Folder\FolderService as Folder;
use App\System\Help\HelpModel as Help;

class APIController extends Controller
{
    protected $api_key;
    protected $get;
    protected $post;
    protected $auth;
    protected $data;

    public function __construct()
    {
        $this->api_key = parse_request('key')[0];
    }

    public function authorize()
    {
        if($this->api_key) {
            $this->get = parse_request('get')[0];
            $this->post = parse_request('post')[0];
        } else {
            echo 'ERROR: Invalid Key!';
            exit;
        }

        if(Host::auth()) {
            $this->auth = 'is_host';
        }

        if(Host::guest()) {
            $this->auth = 'is_guest';
        }

        if(User::auth()) {
            $this->auth = 'is_user';
        }

        $this->auth = 'is_visitor';

        $this->request();
    }

    private function request()
    {
        if(isset($this->get)) {
            $request = $this->get;
        }

        if(isset($this->post)) {
            $request = $this->get;
        }

        switch ($request) {
            case 'auto':
                $this->auto();
                break;
            
            default:
                # code...
                break;
        }
    }

    public function help()
    {
        return Help::select('cmd')->where($this->auth, 1)->get();
    }

    public function files()
    {
        return Host::data()->files()->select('file_name', 'folder_id')->where('folder_id', Folder::id())->get();
    }

    public function folders()
    {
        return Host::data()->folders()->select('folder_name')->get();
    }

    public function auto()
    {
        foreach ($this->help() as $help) {
            $this->data[] = $help->cmd;
        }

        if($this->auth == 'is_host') {
            foreach ($this->files() as $file) {
                $this->data[] = $file->file_name;
            }
            foreach ($this->folders() as $folder) {
                $this->data[] = $folder->folder_name;
            }
        }

        echo json_encode($this->data);
        exit;
    }

}