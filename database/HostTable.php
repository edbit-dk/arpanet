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
            $table->string('host_name')->unique();
            $table->string('password')->nullable();
            $table->string('welcome')->nullable();
            $table->string('org')->nullable();
            $table->string('os')->nullable();
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
                'host_name' => 'sri-nic.arpa', 
                'password' => random_pass(),
                'org' => 'Stanford Research Institute - Network Information Center',
                'welcome' => "ARPANET LOGIN SYSTEM\n Authorized users only.",
                'os' => 'PDP-10 TOPS-20',
                'ip' => '192.5.4.1',
                'network' => 1,
                'level_id' => 1,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'host_name' => 'ucla.edu', 
                'password' => random_pass(),
                'org' => 'University of California',
                'location' => 'Los Angeles',
                'os' => 'SDS Sigma 7',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 1,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'host_name' => 'ucsb.edu', 
                'password' => random_pass(),
                'org' => 'University of California',
                'welcome' => '',
                'os' => 'IBM 360/75 OS/MVT',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 1,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'host_name' => 'uusc.edu', 
                'password' => random_pass(),
                'org' => 'University of Utah School of Computing',
                'welcome' => '',
                'os' => 'DEC PDP-10 TENEX',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 1,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'host_name' => 'nic.ddn.mil', 
                'password' => random_pass(8),
                'org' => 'Military Defense Data Network (UNCLASSIFIED)',
                'welcome' => '****************************************************************
                            * WARNING: This is a U.S. Government Computer System.         *
                            * Unauthorized access is prohibited.                          *
                            * All activities may be monitored and recorded.               *
                            ****************************************************************',
                'os' => '4.3 BSD UNIX 1986',
                'ip' => '192.67.67.20',
                'network' => 1,
                'level_id' => 3,
                'motd' => 'WARNING: Unauthorized access to this system is prohibited.
                All activity may be monitored and recorded.'
            ],
            [
                'user_id' => 1,
                'host_name' => 'dsnet1.mil', 
                'password' => random_pass(8),
                'org' => 'Defense Secure Network 1 (CONFIDENTIAL)',
                'location' => 'USA',
                'os' => '4.3 BSD UNIX 1986',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 4,
                'motd' => 'WARNING: Unauthorized access to this system is prohibited.
                All activity may be monitored and recorded.'
            ],
            [
                'user_id' => 1,
                'host_name' => 'dsnet2.mil', 
                'password' => random_pass(8),
                'org' => 'Defense Secure Network 2 (SECRET)',
                'location' => 'USA',
                'os' => '4.3 BSD UNIX 1986',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 5,
                'motd' => 'WARNING: Unauthorized access to this system is prohibited.
                All activity may be monitored and recorded.'
            ],
            [
                'user_id' => 1,
                'host_name' => 'dsnet3.mil', 
                'password' => random_pass(8),
                'org' => 'Defense Secure Network 3 (TOP SECRET)',
                'location' => 'USA',
                'os' => '4.3 BSD UNIX 1986',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 6,
                'motd' => 'WARNING: Unauthorized access to this system is prohibited.
                All activity may be monitored and recorded.'
            ],
            [
                'user_id' => 1,
                'host_name' => 'telenet.com', 
                'password' => random_pass(),
                'org' => 'Telenet Inc. (BBN)',
                'location' => 'Washington, D.C.',
                'os' => 'Honeywell 316 IMP NCP/X.25',
                'ip' => random_ip(),
                'network' => 1,
                'level_id' => 1,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'host_name' => 'poseido.net', 
                'password' => random_pass(),
                'org' => 'Poseidon Energy Network',
                'welcome' => '',
                'os' => 'RobCo UOS v.84',
                'ip' => random_ip(),
                'network' => 0,
                'level_id' => 2,
                'motd' => ''
            ],
            [
                'user_id' => 1,
                'host_name' => 'spsdd.edu', 
                'password' => 'pencil',
                'org' => 'WELCOME TO THE SEATTLE PUBLIC SCHOOL DISTRICT DATANET',
                'welcome' => 'PLEASE LOGON WITH USER PASSWORD:',
                'os' => 'PDP 11/272 PRS TIP # 45',
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

