<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHasRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by',30)->nullable();
            $table->string('updated_by',30)->nullable();
            $table->string('status',2)->nullable();
            $table->softDeletes();

            $table->string('dataAreaId',4)->nullable();
            $table->string('user_uid',128)->nullable();
            $table->uuid('role_uid')->nullable();
            $table->string('data01',255)->nullable();
            $table->string('data02',255)->nullable();
            $table->timestamp('date01')->nullable();
            $table->timestamp('date02')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_roles');
    }
}
