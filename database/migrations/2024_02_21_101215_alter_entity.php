<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEntity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('entity_uid')->nullable();
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->uuid('entity_uid')->nullable();
        });
        Schema::table('permissions', function (Blueprint $table) {
            $table->uuid('entity_uid')->nullable();
        });
        Schema::table('menus', function (Blueprint $table) {
            $table->uuid('entity_uid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
