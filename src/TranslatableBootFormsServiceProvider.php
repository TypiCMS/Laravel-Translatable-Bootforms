<?php namespace Propaganistas\LaravelTranslatableBootForms;

use Illuminate\Support\ServiceProvider;

class TranslatableBootFormsServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('translatable-bootforms.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'translatable-bootforms'
        );

        // Override BootForm's form builder in order to get model binding
        // between BootForm & TranslatableBootForm working.
        $this->app['adamwathan.form'] = $this->app->share(function ($app) {
            $formBuilder = new Form\FormBuilder();
            $formBuilder->setLocales($this->getLocales());
            $formBuilder->setErrorStore($app['adamwathan.form.errorstore']);
            $formBuilder->setOldInputProvider($app['adamwathan.form.oldinput']);
            $formBuilder->setToken($app['session.store']->getToken());

            return $formBuilder;
        });

        // Define TranslatableBootForm.
        $this->app['translatable-bootform'] = $this->app->share(function ($app) {
            $form = new TranslatableBootForm($app['bootform']);
            $form->locales($this->getLocales());

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
        return array(
            'adamwathan.form',
            'translatable-bootform',
        );
    }
    
    /**
     * Get Translatable's locales.
     * 
     * @return array
     */
    protected function getLocales()
    {
        return with(new Translatable\TranslatableWrapper)->getLocales();
    }

}
