<?php namespace VanushWasHere\LaravelHtmlCssInliner;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use VanushWasHere\LaravelHtmlCssInliner\HtmlCssInlinerPlugin;

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


        $this->mergeConfigFrom(__DIR__ . '/../config/html-css-inliner.php', 'html-css-inliner');

        $this->app->singleton(HtmlCssInlinerPlugin::class, function ($app) {
            return new HtmlCssInlinerPlugin($app['config']->get('html-css-inliner'));
        });

        $this->app->booting(function() {
            $loader = AliasLoader::getInstance();
            $loader->alias('InlineCss', HtmlCssInlinerPlugin::class);
        });

    }
}
