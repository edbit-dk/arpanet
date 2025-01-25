<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

use App\Host\HostModel as Host;

class HostTable extends Host
{
    public static function up()
    {
        Capsule::schema()->dropIfExists((new self)->table);
        
        Capsule::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->constrained()->nullable();
            $table->string('host_name')->unique();
            $table->string('password')->nullable();
            $table->string('location')->nullable();
            $table->string('org')->nullable();
            $table->string('os')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->text('motd')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(1);
            $table->integer('level_id')->default(0);
            $table->timestamps();
        });

        Capsule::table((new self)->table)->insert([
            [
            'host_name' => 'arpanet', 
            'password' => word_pass(),
            'org' => 'Advanced Research Projects Agency Network',
            'location' => 'USA, Virginia',
            'os' => '',
            'ip' => '0.0.0.0'
            ]
        ]);
    }

    public static function down()
    {
        Capsule::schema()->drop((new self)->table);
    }
}

