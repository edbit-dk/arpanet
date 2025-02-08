<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Help\HelpModel as Help;

class HelpTable extends Help
{
    public static function up()
    {
        DB::schema()->dropIfExists((new self)->table);

        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('cmd');
            $table->string('input')->nullable();
            $table->text('info');
            $table->boolean('is_user')->default(0);
            $table->boolean('is_host')->default(0);
            $table->boolean('is_visitor')->default(0);
            $table->boolean('is_guest')->default(0);
        });

        DB::table((new self)->table)->insert([
            [
                'cmd' => 'help', 
                'input' => '[cmd|page]', 
                'info' => 'shows info about command',
                'is_user' => 1,
                'is_host' => 1,
                'is_visitor' => 1,
                'is_guest' => 1
            ],
            [
                'cmd' => 'uplink', 
                'input' => '<access code>', 
                'info' => 'uplink to ARPANET',
                'is_user' => 0,
                'is_host' => 0,
                'is_visitor' => 1,
                'is_guest' => 0
            ],
            [
                'cmd' => 'newuser', 
                'input' => '<username>', 
                'info' => 'create ARPANET account',
                'is_user' => 0,
                'is_host' => 0,
                'is_visitor' => 1,
                'is_guest' => 0
            ],
            [
                'cmd' => 'login', 
                'input' => '<username>', 
                'info' => 'login to ARPANET (alias: logon) ',
                'is_user' => 0,
                'is_host' => 0,
                'is_visitor' => 1,
                'is_guest' => 1
            ],
            [
                'cmd' => 'logout', 
                'input' => NULL, 
                'info' => 'leave host/ARPANET (alias: exit, dc, quit, close) ',
                'is_user' => 1,
                'is_host' => 1,
                'is_visitor' => 0,
                'is_guest' => 1
            ],
            [
                'cmd' => 'ver', 
                'input' => NULL, 
                'info' => 'OS version',
                'is_user' => 1,
                'is_host' => 1,
                'is_visitor' => 1,
                'is_guest' => 1
            ],
            [
                'cmd' => 'music', 
                'input' => '<start|stop|next>', 
                'info' => 'play 80s music',
                'is_user' => 1,
                'is_host' => 1,
                'is_visitor' => 1,
                'is_guest' => 1
            ],
            [
                'cmd' => 'color', 
                'input' => '<green|white|yellow|blue>', 
                'info' => 'terminal color',
                'is_user' => 1,
                'is_host' => 1,
                'is_visitor' => 1,
                'is_guest' => 1
            ],
            [
                'cmd' => 'term', 
                'input' => '<DEC-VT100|IBM-3270>', 
                'info' => 'change terminal mode',
                'is_user' => 1,
                'is_host' => 1,
                'is_visitor' => 1,
                'is_guest' => 1
            ],
            [
                'cmd' => 'netstat', 
                'input' => NULL, 
                'info' => 'list connected nodes (alias: scan)',
                'is_user' => 1,
                'is_host' => 1,
                'is_visitor' => 0,
                'is_guest' => 0
            ],
            [
                'cmd' => 'telnet', 
                'input' => '<host>', 
                'info' => 'connect to host (alias: connect)',
                'is_user' => 1,
                'is_host' => 1,
                'is_visitor' => 0,
                'is_guest' => 0
            ],
            [
                'cmd' => 'mail', 
                'input' => '[send|read|list|delete]', 
                'info' => "email user: -s <subject> <user>[@host] < <body> \r\n
                        list emails: [-l] \r\n
                        read email: [-r] <ID> \r\n
                        sent emails: -s \r\n
                        sent email: -s <ID> \r\n
                        delete email: -d <ID>",
                'is_user' => 1,
                'is_host' => 1,
                'is_visitor' => 0,
                'is_guest' => 0
            ],
            [
                'cmd' => 'cd', 
                'input' => '[folder]', 
                'info' => 'change directory',
                'is_user' => 0,
                'is_host' => 1,
                'is_visitor' => 0,
                'is_guest' => 1
            ],
            [
                'cmd' => 'ls', 
                'input' => NULL, 
                'info' => 'list files on host (alias: dir)',
                'is_user' => 0,
                'is_host' => 1,
                'is_visitor' => 0,
                'is_guest' => 1
            ],
            [
                'cmd' => 'cat', 
                'input' => '<filename>', 
                'info' => 'print contents of file (alias: more, open)',
                'is_user' => 0,
                'is_host' => 1,
                'is_visitor' => 0,
                'is_guest' => 1
            ],
            [
                'cmd' => 'set', 
                'input' => '<command>', 
                'info' => 'TERMINAL/INQUIRE, FILE/PROTECTION=OWNER:RWED /sys/passwd, HALT RESTART/MAINT',
                'is_user' => 0,
                'is_host' => 0,
                'is_visitor' => 0,
                'is_guest' => 1
            ],
            [
                'cmd' => 'run', 
                'input' => '<command>', 
                'info' => 'DEBUG /sys/passwd',
                'is_user' => 0,
                'is_host' => 0,
                'is_visitor' => 0,
                'is_guest' => 1
            ],
            [
                'cmd' => 'debug', 
                'input' => '[dump]', 
                'info' => 'run memory dump',
                'is_user' => 0,
                'is_host' => 0,
                'is_visitor' => 0,
                'is_guest' => 1
            ],
        ]);
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

