<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_notification', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->uuid('entity_uid')->nullable();
            $table->string('dataAreaId')->nullable();
            $table->uuid('job_uid')->nullable();
            $table->uuid('doc_uid')->nullable();
            $table->string('doc_type')->nullable();
            $table->string('doc_category')->nullable(); // Email / WA Notification
            $table->string('send_to')->nullable();
            $table->timestamp('send_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_notification');
    }
}
