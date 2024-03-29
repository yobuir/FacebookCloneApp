<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
              $table->id();
            $table->unsignedBigInteger('user_id')->constrained()->ondelete('cascade');
            $table->unsignedBigInteger('post_id')->constrained()->ondelete('cascade');;
            $table->boolean('liked');
            $table->timestamps();
            $table->unique(['user_id','post_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
