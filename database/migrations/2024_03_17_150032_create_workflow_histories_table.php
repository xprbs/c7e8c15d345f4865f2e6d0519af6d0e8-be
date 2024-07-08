<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('status',2)->nullable();
            $table->softDeletes();
            $table->uuid('entity_uid')->nullable();
            $table->string('dataAreaId')->nullable();
            $table->string('doc_type')->nullable();
            $table->uuid('doc_uid')->nullable();
            $table->uuid('user_uid')->nullable();
            $table->string('user_name')->nullable();
            $table->string('priority')->nullable();
            $table->string('approval')->nullable()->comment('1 waiting approval, 2 approve, 3 reject');
            $table->text('command')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflow_histories');
    }
}
