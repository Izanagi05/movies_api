<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watchlists', function (Blueprint $table) {
           $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('movie_api_id');
            $table->enum('status', ['want_to_watch','watching','watched'])->default('want_to_watch');
            $table->decimal('rating',3,1)->nullable();
            // $table->tinyInteger('rating')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'movie_api_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('watchlists');
    }
};
