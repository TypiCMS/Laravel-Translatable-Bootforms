<?php

declare(strict_types=1);

namespace TypiCMS\LaravelTranslatableBootForms\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use TypiCMS\BootForms\BootFormsServiceProvider;
use TypiCMS\BootForms\Facades\BootForm;
use TypiCMS\LaravelTranslatableBootForms\Facades\TranslatableBootForm;
use TypiCMS\LaravelTranslatableBootForms\TranslatableBootFormsServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BootFormsServiceProvider::class,
            TranslatableBootFormsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'BootForm' => BootForm::class,
            'TranslatableBootForm' => TranslatableBootForm::class,
        ];
    }
}
