<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEntityToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_checklist_answer', function (Blueprint $table) {
            $table->string('entity_uid')->nullable();
        });

        Schema::table('note_histories', function (Blueprint $table) {
            $table->string('entity_uid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_checklist_answer', function (Blueprint $table) {
            $table->string('entity_uid')->nullable();
        });

        Schema::table('note_histories', function (Blueprint $table) {
            $table->string('entity_uid')->nullable();
        });
    }
}
