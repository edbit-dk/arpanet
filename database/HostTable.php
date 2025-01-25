<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Host\HostModel as Host;

class HostTable extends Host
{
    public static function up()
    {
        DB::schema()->dropIfExists((new self)->table);
        
        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->constrained()->nullable();
            $table->string('host_name')->unique();
            $table->string('password')->nullable();
            $table->string('location')->nullable();
            $table->string('org')->nullable();
            $table->string('os')->nullable();
            $table->ipAddress('ip')->unique();
            $table->text('motd')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(1);
            $table->integer('level_id')->default(0);
            $table->timestamps();
        });

        DB::table((new self)->table)->insert([
            [
                'host_name' => 'arpanet', 
                'password' => word_pass(),
                'org' => 'Advanced Research Projects Agency Network',
                'location' => 'USA, Virginia',
                'os' => 'AT&T UNIX Operating System (TeleTerm) v.1.0 Copyright 1975-1977 AT&T',
                'ip' => '0.0.0.0'
            ],
            [
                'host_name' => 'nsfnet', 
                'password' => word_pass(),
                'org' => 'Academic Research Network',
                'location' => 'USA',
                'os' => 'DEC Vax-8600 4.3BSD',
                'ip' => random_ip(),
                'level_id' => 1,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'ucla', 
                'password' => word_pass(),
                'org' => 'University of California',
                'location' => 'Los Angeles',
                'os' => 'SDS Sigma 7',
                'ip' => random_ip(),
                'level_id' => 1,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'arc', 
                'password' => word_pass(),
                'org' => 'Augmentation Research Center',
                'location' => 'Menlo Park, California',
                'os' => 'SDS 940 NLS "Genie" InterNIC',
                'ip' => random_ip(),
                'level_id' => 1,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'ucsb', 
                'password' => word_pass(),
                'org' => 'University of California',
                'location' => 'Santa Babara',
                'os' => 'IBM 360/75 OS/MVT',
                'ip' => random_ip(),
                'level_id' => 1,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'uusc', 
                'password' => word_pass(),
                'org' => 'University of Utah School of Computing',
                'location' => 'Salt Lake City, Utah',
                'os' => 'DEC PDP-10 TENEX',
                'ip' => random_ip(),
                'level_id' => 1,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'telenet', 
                'password' => word_pass(),
                'org' => 'Telenet Inc. (BBN)',
                'location' => 'Washington, D.C.',
                'os' => 'Honeywell 316 IMP NCP/X.25',
                'ip' => random_ip(),
                'level_id' => 1,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'poseidonet', 
                'password' => word_pass(),
                'org' => 'Poseidon Energy Network',
                'location' => 'Boston, Massachusetts',
                'os' => 'RobCo UOS v.84',
                'ip' => random_ip(),
                'level_id' => 2,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'milnet', 
                'password' => word_pass(),
                'org' => 'Military Defense Data Network (UNCLASSIFIED)',
                'location' => 'USA',
                'os' => '4.3 BSD UNIX 1986',
                'ip' => random_ip(),
                'level_id' => 3,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'dsnet1', 
                'password' => word_pass(),
                'org' => 'Defense Secure Network 1 (CONFIDENTIAL)',
                'location' => 'USA',
                'os' => '4.3 BSD UNIX 1986',
                'ip' => random_ip(),
                'level_id' => 4,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'dsnet2', 
                'password' => word_pass(),
                'org' => 'Defense Secure Network 2 (SECRET)',
                'location' => 'USA',
                'os' => '4.3 BSD UNIX 1986',
                'ip' => random_ip(),
                'level_id' => 5,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
            [
                'host_name' => 'dsnet3', 
                'password' => word_pass(),
                'org' => 'Defense Secure Network 3 (TOP SECRET)',
                'location' => 'USA',
                'os' => '4.3 BSD UNIX 1986',
                'ip' => random_ip(),
                'level_id' => 6,
                'motd' => 'WARNING: All connections are monitored and logged. 
                Any malicious and/or unauthorized activity is strictly prohibited!'
            ],
        ]);
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

