<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditChecklistAnswerModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Menyimpan jawaban 
        Schema::create('audit_checklist_answer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->string('dataAreaId')->nullable();
            $table->uuid('audit_uid')->nullable(); // Audit uid
            $table->uuid('question_uid')->nullable(); // Question uid
            $table->uuid('question_detail_uid')->nullable(); // Question detail id
            $table->string('answer')->nullable(); // Answer
            $table->text('answer_description')->nullable(); // Answer Description
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_checklist_answer');
    }
}
