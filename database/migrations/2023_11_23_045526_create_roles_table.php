<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by',30)->nullable();
            $table->string('updated_by',30)->nullable();
            $table->string('status',2)->nullable();
            $table->softDeletes();

            $table->string('dataAreaId',4)->nullable();
            $table->uuid('role_uid')->nullable()->unique();
            $table->string('role_group',128)->nullable();
            $table->string('role_name',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
