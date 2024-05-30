<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveillanceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveillance_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('status',2)->nullable();
            $table->softDeletes();
            $table->uuid('entity_uid')->nullable();
            $table->uuid('project_uid')->nullable();
            $table->string('dataAreaId')->nullable();
            $table->string('doc_type')->nullable();
            $table->text('note')->nullable();
            $table->string('path')->nullable();
            $table->string('filename')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surveillance_history');
    }
}
