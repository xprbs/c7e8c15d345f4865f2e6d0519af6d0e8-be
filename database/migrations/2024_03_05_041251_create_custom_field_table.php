<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_field', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->string('status',2)->nullable();
            $table->string('doc_type')->nullable();
            $table->uuid('field_uid')->nullable();
            $table->string('field_key')->nullable();
            $table->string('field_type')->nullable();
            $table->string('field_length')->nullable();
            $table->string('field_name')->nullable();
            $table->string('field_desc')->nullable();
            $table->string('field_01')->nullable();
            $table->string('field_02')->nullable();
            $table->string('field_03')->nullable();
            $table->string('field_04')->nullable();
            $table->string('field_05')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_field');
    }
}
