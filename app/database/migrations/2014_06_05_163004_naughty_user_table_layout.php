<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NaughtyUserTableLayout extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // Creates the failservers table
        Schema::create('failservers', function($table) {
            $table->engine = 'InnoDB';
            $table->string('server');
            $table->string('timestamp');
        });
        //create highuser table
        Schema::create('highuser', function($table) {
            $table->engine = 'InnoDB';
            $table->string('server');
            $table->string('username');
            $table->float('diskspace');
            $table->string('owner');
            $table->float('owner_diskspace');
            $table->float('owner_diskallowed');
            $table->date('reportran_at');
            $table->date('created_at');
        });
        //create resellerstats table
        Schema::create('resellerstats', function($table) {
            $table->engine = 'InnoDB';
            $table->string('server');
            $table->string('reseller');
            $table->integer('number_of_accounts');
            $table->float('diskspace_in_gb');
            $table->date('reportran_at');
        });
        //create table for response time logging and count of naughty users
        Schema::create('response', function($table) {
            $table->engine = 'InnoDB';
            $table->string('server');
            $table->string('badusers');
            $table->string('timestamp');
        });
        Schema::create('servers', function($table) {
            $table->engine = 'InnoDB';
            $table->string('name');
            $table->string('url');
            $table->string('ip');
            $table->string('id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }

}
