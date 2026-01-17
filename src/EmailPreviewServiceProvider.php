<?php

namespace KafeinStudio\EmailPreview;



class EmailPreviewServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->loadViewsFrom(__DIR__ . '/views', 'laravel-email-preview');

        $this->app['router']->get('/emails-preview')->uses(EmailPreviewController::class.'@list')->name('emailpreview.list');
        $this->app['router']->get('/emails-preview/download/{emailName}')->uses(EmailPreviewController::class.'@download')->name('emailpreview.download');
        $this->app['router']->get('/emails-preview/{emailName}')->uses(EmailPreviewController::class.'@show')->name('emailpreview.show');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/emailpreview.php', 'emailpreview');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([__DIR__ . '/../config/emailpreview.php' => config_path('emailpreview.php')], 'config');
    }
}
