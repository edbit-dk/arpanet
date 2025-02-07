<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Host\HostModel as Host;

class HostTable extends Host
{
    public static function up()
    {
        DB::schema()->disableForeignKeyConstraints();
        DB::schema()->dropIfExists((new self)->table);
        
        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('hostname')->unique();
            $table->string('password')->nullable();
            $table->text('welcome')->nullable();
            $table->string('org')->nullable();
            $table->string('os')->nullable();
            $table->string('location')->nullable();
            $table->ipAddress('ip')->unique();
            $table->text('motd')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('network')->default(0);
            $table->integer('level_id')->default(1);
            $table->timestamps();
        });

        DB::table((new self)->table)->insert([
            [
                'user_id' => 1,
                'hostname' => 'sri.nic.arpa', 
                'password' => random_pass(),
                'org' => 'SRI - NIC',
                'location' => 'Menlo Park, CA',
                'welcome' => "",
                'os' => 'VAX-11/750 TOPS-20',
                'ip' => '192.5.4.1',
                'network' => 1,
                'level_id' => 1,
                'motd' => null
            ],
            [
                'user_id' => 1,
                'hostname' => 'ucla.edu', 
                'password' => random_pass(),
                'org' => 'University of California',
                'location' => 'Los Angelos, CA',
                'welcome' => 'Authorized users only.',
                'os' => 'VAX-11/780 4.3BSD UNIX',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 1,
                'motd' => null
            ],
            [
                'user_id' => 1,
                'hostname' => 'ucsb.edu', 
                'password' => random_pass(),
                'org' => 'University of California',
                'location' => 'Santa Barbara, CA',
                'welcome' => 'Authorized users only.',
                'os' => 'VAX-11/780 4.3BSD UNIX',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 1,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'hostname' => 'uusc.edu', 
                'password' => random_pass(),
                'org' => 'University of Utah School of Computing',
                'location' => 'Salt Lake City, UT',
                'welcome' => 'Authorized users only.',
                'os' => 'VAX-11/750 4.3BSD UNIX',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 1,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'hostname' => 'nic.ddn.mil', 
                'password' => random_pass(8),
                'org' => 'Military Defense Data Network (UNCLASSIFIED)',
                'location' => '',
                'welcome' => '*** WARNING: UNAUTHORIZED USE PROHIBITED ***',
                'os' => 'UNIX System V AT&T',
                'ip' => '192.67.67.20',
                'network' => 1,
                'level_id' => 3,
                'motd' => 'WARNING: All activity may be monitored and recorded.'
            ],
            [
                'user_id' => 1,
                'hostname' => 'dsnet1.mil', 
                'password' => random_pass(8),
                'org' => 'Defense Secure Network 1 (CONFIDENTIAL)',
                'location' => '',
                'welcome' => '*** WARNING: UNAUTHORIZED USE PROHIBITED ***',
                'os' => 'VAX/VMS UNIX',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 4,
                'motd' => 'WARNING: All activity may be monitored and recorded.'
            ],
            [
                'user_id' => 1,
                'hostname' => 'dsnet2.mil', 
                'password' => random_pass(8),
                'org' => 'Defense Secure Network 2 (SECRET)',
                'location' => '',
                'welcome' => '*** WARNING: UNAUTHORIZED USE PROHIBITED ***',
                'os' => 'VAX/VMS UNIX',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 5,
                'motd' => 'WARNING: All activity may be monitored and recorded.'
            ],
            [
                'user_id' => 1,
                'hostname' => 'dsnet3.mil', 
                'password' => random_pass(8),
                'org' => 'Defense Secure Network 3 (TOP SECRET)',
                'location' => '',
                'welcome' => '',
                'os' => 'VAX/VMS UNIX',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 6,
                'motd' => 'WARNING: Unauthorized access to this system is prohibited.
                All activity may be monitored and recorded.'
            ],
            [
                'user_id' => 1,
                'hostname' => 'telenet.com', 
                'password' => random_pass(),
                'org' => 'Telenet Inc. (BBN)',
                'location' => '',
                'welcome' => '',
                'os' => 'PDP-11/IMP 4.3BSD UNIX',
                'ip' => random_ip(),
                'network' => 1,
                'level_id' => 1,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'hostname' => 'poseido.net', 
                'password' => random_pass(),
                'org' => 'Poseidon Energy Network',
                'location' => 'Commonwealth, Boston',
                'welcome' => '',
                'os' => 'RobCo UOS v.84',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 2,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'hostname' => 'spsdd.edu', 
                'password' => 'pencil',
                'org' => 'WELCOME TO THE SEATTLE PUBLIC SCHOOL DISTRICT DATANET',
                'location' => 'Seattle',
                'welcome' => 'PLEASE LOGON WITH USER PASSWORD:',
                'os' => 'PDP-11/272 PRS TIP # 45',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 2,
                'motd' => ''
            ]
        ]);
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

