<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->string('dataAreaId')->nullable();
            $table->uuid('audit_id')->nullable(); // Audit uid
            $table->uuid('question_uid')->nullable(); // Header uid
            $table->uuid('question_detail_uid')->nullable(); // Detail uid
            $table->text('note')->nullable(); // Note
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_histories');
    }
}
