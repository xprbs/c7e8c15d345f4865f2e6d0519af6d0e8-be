<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by',30)->nullable();
            $table->string('updated_by',30)->nullable();
            $table->string('status',2)->nullable();
            $table->softDeletes();

            $table->string('dataAreaId',4)->nullable();
            $table->uuid('menus_uid')->nullable()->unique();
            $table->string('menus_type',128)->nullable();
            $table->string('menus_name',255)->nullable();
            $table->string('url',255)->nullable();
            $table->string('acl_action',128)->nullable();
            $table->string('acl_subject',128)->nullable();
            $table->string('icon',128)->nullable();
            $table->string('level',8)->nullable();
            $table->uuid('parent_id')->nullable();
            $table->integer('order_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
