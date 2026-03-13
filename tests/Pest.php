<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as DB;
use TypiCMS\LaravelTranslatableBootForms\Tests\Models\Model;
use TypiCMS\LaravelTranslatableBootForms\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function (): void {
        $this->app['config']->set('typicms.locales', ['en' => 'en_US', 'nl' => 'nl_NL']);
        $this->app['config']->set('translatable-bootforms.label-locale-indicator', '%label (%locale)');

        $this->bootform = $this->app->make('typicms.bootform');
        $this->form = $this->app->make('translatable-bootform');
    })
    ->in('.');

function configureDatabase(): void
{
    $db = new DB;
    $db->addConnection([
        'driver' => 'sqlite',
        'database' => ':memory:',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ]);
    $db->bootEloquent();
    $db->setAsGlobal();

    DB::schema()->create('models', function ($table): void {
        $table->id();
        $table->string('default');
        $table->text('input');
    });

    Model::create(['default' => 'model', 'input' => ['en' => 'translation', 'nl' => 'vertaling']]);
}
