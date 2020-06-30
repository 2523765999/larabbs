<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepliesTable extends Migration
{
	public function up()
	{
		Schema::create('replies', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('topic_id')->unsigned()->default(0)->index();
//            $table->bigInteger('user_id')->unsigned()->default(0)->index();
//            Illuminate\Database\QueryException  : SQLSTATE[HY000]: General error: 1215 Cannot add foreign key constraint (SQL: alter table `replies` add constraint `replies_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade)
            $table->integer('user_id')->unsigned()->default(0)->index();
            $table->text('content');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('replies');
	}
}
