<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreamChunksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_chunks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stream_id');
            $table->unsignedInteger('chunk_id'); // 0 = header
            $table->string('filename');
            $table->unsignedInteger('cluster_offset')->nullable();
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
        Schema::dropIfExists('stream_chunks');
    }
}
