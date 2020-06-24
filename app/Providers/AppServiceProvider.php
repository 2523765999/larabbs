<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
	{
		\App\Models\User::observe(\App\Observers\UserObserver::class);
		\App\Models\Reply::observe(\App\Observers\ReplyObserver::class);
		\App\Models\Topic::observe(\App\Observers\TopicObserver::class);

        \Carbon\Carbon::setLocale('zh');//diffForhumans() 英语格式转化为中文

        DB::listen(function ($query) {
//            dump($query->sql);echo PHP_EOL;//好处是比var_dump或者echo 有颜色区别；
//            dump($query->bindings);echo PHP_EOL;
//            dump(vsprintf(str_replace('?', '%s', $query->sql), $query->bindings));
            Log::info('info',array('sql' => vsprintf(str_replace('?', '%s', $query->sql), $query->bindings)));
            // $query->time
        });


    }
}
