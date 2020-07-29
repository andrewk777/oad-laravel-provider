<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('hash')->unique();
			$table->primary('hash');
            $table->string('file_name')->nullable();
            $table->string('path')->nullable();
            $table->string('ext',15)->nullable();
            $table->string('mime')->nullable();
            $table->string('size')->nullable();
            $table->string('attachment_id')->nullable();
			$table->string('attachment_type')->nullable();
            $table->string('attachment_field')->nullable();
			$table->boolean('is_saved')->default(false);
            $table->string('user_updated');
            $table->string('user_created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
