<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('attaches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('ext', 12);
            $table->integer('size');
            $table->integer('priority');
            $table->string('block');
            $table->integer('attachable_id');
            $table->string('attachable_type');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('attaches');
    }
}
