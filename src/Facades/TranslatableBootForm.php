<?php

declare(strict_types=1);

namespace TypiCMS\LaravelTranslatableBootForms\Facades;

use Illuminate\Support\Facades\Facade;

class TranslatableBootForm extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'translatable-bootform';
    }
}
