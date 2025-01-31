<?php

namespace App\System;

use Lib\Controller;

use DB\LevelTable;
use DB\UserTable;
use DB\HostTable;
use DB\HostUserTable;
use DB\HostNodeTable;
use DB\HelpTable;
use DB\EmailTable;
use DB\FileTable;
use DB\FolderTable;

class SetupController extends Controller
{
    public function install()
    {
        $this->system();
        $this->users();
        $this->hosts();
        $this->relations();
        $this->folders();
        $this->files();
    }

    public function system()
    {
        LevelTable::up();
        EmailTable::up();
        HelpTable::up();
    }

    public function relations()
    {
        HostUserTable::up();
        HostNodeTable::up();
    }

    public function users()
    {
        UserTable::up();
    }

    public function hosts()
    {
        HostTable::up();
    }

    public function folders()
    {
        FolderTable::up();
    }

    public function files()
    {
        FileTable::up();
    }

    
}