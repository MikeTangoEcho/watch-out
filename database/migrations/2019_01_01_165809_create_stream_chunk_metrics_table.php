<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreamChunkMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_chunk_metrics', function (Blueprint $table) {
            $table->unsignedBigInteger('stream_id');
            $table->unsignedInteger('chunk_id'); // 0 = header
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();

            $table->foreign('stream_id')->references('id')->on('streams')->onDelete('cascade');
            $table->primary(['stream_id', 'chunk_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stream_chunk_metrics', function (Blueprint $table) {
            $table->dropForeign(['stream_id']);
        });
        Schema::dropIfExists('stream_chunk_metrics');
    }
}
