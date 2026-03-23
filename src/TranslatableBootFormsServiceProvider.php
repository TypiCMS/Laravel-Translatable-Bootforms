<?php

declare(strict_types=1);

namespace TypiCMS\LaravelTranslatableBootForms;

use Illuminate\Contracts\Foundation\Application;
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
        $this->app->singleton('typicms.form', function (Application $application): FormBuilder {
            $locales = array_keys(config('typicms.locales', []));
            if ($locales === []) {
                $locales = config('typicms.locales');
            }

            $formBuilder = new FormBuilder;
            $formBuilder->setLocales($locales);
            $formBuilder->setErrorStore($application['typicms.form.errorstore']);
            $formBuilder->setOldInputProvider($application['typicms.form.oldinput']);
            $formBuilder->setToken($application['session.store']->token());

            return $formBuilder;
        });

        // Define TranslatableBootForm.
        $this->app->singleton('translatable-bootform', function (Application $application): TranslatableBootForm {
            $translatableBootForm = new TranslatableBootForm($application['typicms.bootform']);
            $locales = array_keys(config('typicms.locales', []));
            if ($locales === []) {
                $locales = config('typicms.locales');
            }

            $translatableBootForm->locales($locales);

            return $translatableBootForm;
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'typicms.form',
            'translatable-bootform',
        ];
    }
}
