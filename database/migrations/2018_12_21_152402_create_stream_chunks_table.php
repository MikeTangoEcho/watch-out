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
            $table->unsignedInteger('filesize')->default(0);
            $table->unsignedInteger('cluster_offset')->nullable();
            $table->timestamps();

            $table->foreign('stream_id')->references('id')->on('streams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stream_chunks', function (Blueprint $table) {
            $table->dropForeign(['stream_id']);
        });
        Schema::dropIfExists('stream_chunks');
    }
}
