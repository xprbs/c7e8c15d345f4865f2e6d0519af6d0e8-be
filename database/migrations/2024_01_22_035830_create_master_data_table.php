<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->string('dataAreaId')->nullable();
            $table->string('group_type')->nullable();
            $table->string('doc_type')->nullable();
            $table->string('key1')->nullable();
            $table->string('key2')->nullable();
            $table->string('key3')->nullable();
            $table->string('key4')->nullable();
            $table->string('value1')->nullable();
            $table->string('value2')->nullable();
            $table->string('value3')->nullable();
            $table->string('value4')->nullable();
            $table->timestamp('date1')->nullable();
            $table->timestamp('date2')->nullable();
            $table->timestamp('date3')->nullable();
            $table->timestamp('date4')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_data');
    }
}
