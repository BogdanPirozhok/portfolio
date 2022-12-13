<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE');

            $table->unsignedInteger('initial_user_id')->nullable();
            $table->foreign('initial_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE');

            $table->unsignedInteger('post_id')->nullable();
            $table->foreign('post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('CASCADE');

            $table->unsignedInteger('comment_id')->nullable();
            $table->foreign('comment_id')
                ->references('id')
                ->on('comments')
                ->onDelete('CASCADE');

            $table->boolean('is_read')->default(false);

            $table->text('slug');
            $table->text('title')->nullable();
            $table->text('text')->nullable();

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
        Schema::dropIfExists('notifications');
    }
};
