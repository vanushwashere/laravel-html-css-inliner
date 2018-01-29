<?php namespace VanushWasHere\LaravelHtmlCssInliner;

use Illuminate\Support\ServiceProvider;
use VanushWasHere\LaravelHtmlCssInliner\CssInlinerPlugin;

class LaravelHtmlCssInlinerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/html-css-inliner.php' => config_path('html-css-inliner.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/css-inliner.php', 'css-inliner');

        $this->app->singleton(CssInlinerPlugin::class, function ($app) {
            return new CssInlinerPlugin($app['config']->get('css-inliner'));
        });

     /*   $this->app->extend('swift.mailer', function (Swift_Mailer $swiftMailer, $app) {
            $inlinerPlugin = $app->make(CssInlinerPlugin::class);
            $swiftMailer->registerPlugin($inlinerPlugin);
            return $swiftMailer;
        });*/
    }
}
