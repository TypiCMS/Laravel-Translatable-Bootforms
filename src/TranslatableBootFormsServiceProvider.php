<?php

namespace TypiCMS\LaravelTranslatableBootForms;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use TypiCMS\LaravelTranslatableBootForms\Form\FormBuilder;

class TranslatableBootFormsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('translatable-bootforms.php'),
        ], 'typicms-config');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'translatable-bootforms');

        // Override BootForm's form builder in order to get model binding
        // between BootForm & TranslatableBootForm working.
        $this->app->singleton('typicms.form', function ($app) {
            $locales = array_keys(config('typicms.locales', []));
            if (empty($locales)) {
                $locales = config('translatable-bootforms.locales');
            }
            $formBuilder = new FormBuilder();
            $formBuilder->setLocales($locales);
            $formBuilder->setErrorStore($app['typicms.form.errorstore']);
            $formBuilder->setOldInputProvider($app['typicms.form.oldinput']);
            $formBuilder->setToken($app['session.store']->token());

            return $formBuilder;
        });

        // Define TranslatableBootForm.
        $this->app->singleton('translatable-bootform', function ($app) {
            $form = new TranslatableBootForm($app['typicms.bootform']);
            $locales = array_keys(config('typicms.locales', []));
            if (empty($locales)) {
                $locales = config('translatable-bootforms.locales');
            }
            $form->locales($locales);

            return $form;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'typicms.form',
            'translatable-bootform',
        ];
    }
}
