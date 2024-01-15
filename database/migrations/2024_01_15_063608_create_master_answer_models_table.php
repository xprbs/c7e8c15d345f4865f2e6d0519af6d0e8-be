<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterAnswerModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_answer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->string('dataAreaId')->nullable();
            $table->uuid('question_answer_uid')->nullable(); 
            $table->string('question_answer_category')->nullable(); // category jawaban = pilihan Yes/No atau Essay atau pilihan 1,2,3,4,5
            $table->string('question_answer_key')->nullable(); // key answer
            $table->string('question_answer_description')->nullable(); // description answer
            $table->string('question_answer_value')->nullable(); // nilai jawaban
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_answer');
    }
}
