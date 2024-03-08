<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveillancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveillances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('status',2)->nullable();
            $table->softDeletes();
            $table->uuid('entity_uid')->nullable();
            $table->uuid('project_uid')->nullable();
            $table->string('dataAreaId')->nullable();
            $table->string('project_location')->nullable();
            $table->string('project_number')->nullable();
            $table->string('project_name')->nullable();
            $table->string('project_category')->nullable();
            $table->timestamp('project_date')->nullable(); 
            $table->timestamp('due_date')->nullable(); 
            $table->text('finding')->nullable();
            $table->text('recommendation')->nullable();
            $table->text('risk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surveillances');
    }
}
