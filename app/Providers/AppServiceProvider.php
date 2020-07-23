<?php

namespace App\Providers;

use App\Models\Link;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\ServiceProvider;
use Log;
use Illuminate\Support\Facades\DB;
use VIACreative\SudoSu\SudoSu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (app()->isLocal()) {
//            $this->app->register(SudoSu::class);// 要写全路径 否则报错  In SudoSu.php line 19:
//  Too few arguments to function VIACreative\SudoSu\SudoSu::__construct(), 1 passed in /home/vagrant/Code/larabbs/vendor/laravel/framework/src/Illuminate/Fo
//  undation/Application.php on line 662 and exactly 3 expected
            $this->app->register('VIACreative\SudoSu\ServiceProvider');
        }
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
        \App\Models\Link::observe(\App\Observers\LinkObserver::class);
        \Carbon\Carbon::setLocale('zh');//diffForhumans() 英语格式转化为中文

        DB::listen(function ($query) {
//            dump($query->sql);echo PHP_EOL;//好处是比var_dump或者echo 有颜色区别；
//            dump($query->bindings);echo PHP_EOL;
//            dump(vsprintf(str_replace('?', '%s', $query->sql), $query->bindings));
            Log::info('info',array('sql' => vsprintf(str_replace('?', '%s', $query->sql), $query->bindings)));
            // $query->time
        });

        //api 用
        Resource::withoutWrapping();
    }
}
