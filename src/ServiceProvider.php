<?php
/**
 * Created by PhpStorm.
 * Title：
 * User: yaokai
 * Date: 2019/8/13 0013
 * Time: 14:49
 */

namespace Xzyk\Weather;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Weather::class, function () {
            return new Weather(config('services.weather.key'));
        });

        $this->app->alias(Weather::class, 'weather');
    }

    public function provides()
    {
        return [ Weather::class, 'weather' ];
    }
}