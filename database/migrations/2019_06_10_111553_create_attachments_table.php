<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateAttachmentsTable
 */
class CreateAttachmentsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attachment_type_id')->comment('附件类型id');
            $table->string('url')->comment('附件url');
            $table->timestamps();
            $table->boolean('enabled');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('attachments');
    }
    
}
