<?php

namespace App\Setup;

use DB\LevelTable;
use DB\UserTable;
use DB\HostTable;
use DB\HostUserTable;
use DB\HostNodeTable;
use DB\HelpTable;
use DB\EmailTable;
use DB\FileTable;
use DB\FolderTable;

class SetupService
{
    public static function install()
    {
        self::system();
        self::users();
        self::hosts();
        self::relations();
        self::folders();
        self::files();
        self::help();
    }

    public static function system()
    {
        LevelTable::up();
        EmailTable::up();
        HelpTable::up();
    }

    public static function relations()
    {
        HostUserTable::up();
        HostNodeTable::up();
    }

    public static function users()
    {
        UserTable::up();
    }

    public static function hosts()
    {
        HostTable::up();
    }

    public static function folders()
    {
        FolderTable::up();
    }

    public static function files()
    {
        FileTable::up();
    }

    public static function help()
    {
        HelpTable::up();
    }

    
}