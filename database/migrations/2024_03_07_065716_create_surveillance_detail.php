<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveillanceDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveillance_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('status',2)->nullable();
            $table->softDeletes();
            $table->uuid('entity_uid')->nullable();
            $table->uuid('project_uid')->nullable();
            $table->string('dataAreaId')->nullable();
            $table->string('image')->nullable();
            $table->string('geo_location')->nullable();
            $table->text('description')->nullable();
            $table->text('comment01')->nullable();
            $table->text('comment02')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surveillance_detail');
    }
}
