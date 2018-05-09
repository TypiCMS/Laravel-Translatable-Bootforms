# Laravel Translatable BootForms

[![Build Status](https://travis-ci.org/TypiCMS/Laravel-Translatable-Bootforms.svg?branch=master)](https://travis-ci.org/TypiCMS/Laravel-Translatable-Bootforms)
[![Coverage Status](https://coveralls.io/repos/github/TypiCMS/Laravel-Translatable-Bootforms/badge.svg?branch=master)](https://coveralls.io/github/TypiCMS/Laravel-Translatable-Bootforms?branch=master)
[![StyleCI](https://styleci.io/repos/56333065/shield?branch=master)](https://styleci.io/repos/56333065)

Make [BootForms](https://github.com/TypiCMS/bootforms) work flawlessly with [Laravel Translatable](https://github.com/spatie/laravel-translatable)!

By importing this package, generating translatable forms using BootForms is a breeze.

This package is adapted from [Propaganistas/Laravel-Translatable-Bootforms](https://github.com/Propaganistas/Laravel-Translatable-Bootforms).

### Installation

1. Run the Composer require command to install the package

    ```
    composer require typicms/laravel-translatable-bootforms
    ```

The package will be autodiscovered by Laravel.

2. In your app config, add the Facade to the `$aliases` array

    ```php
    'aliases' => [
        ...
        'TranslatableBootForm' => TypiCMS\LaravelTranslatableBootForms\Facades\TranslatableBootForm::class,
    ],
    ```

3. Publish the configuration file

    ```bash
    php artisan vendor:publish --provider="TypiCMS\LaravelTranslatableBootForms\TranslatableBootFormsServiceProvider" --tag="config"
    ```

### Usage

Simply use the `TranslatableBootForm` Facade as if it were `BootForm`! That’s it. Multiple form inputs will now be generated for the locales set in Translatable’s configuration file. They will have the corresponding value for each language and will save all of the translations without any code manipulation.

Please review [BootForms’ documentation](https://github.com/typicms/bootforms#using-bootforms) if you’re unsure how to use it.

Example:

```php
// View
{!! BootForm::text('Name', 'name')
            ->placeholder('My placeholder') !!}

// Output
<div class="form-group">
    <label for="name">Name</label>
    <input type="text" name="name" class="form-control" placeholder="My Placeholder">
</div>

// Controller
public function postEdit($request)
{
    $someModel->save($request->all());
}
```

```php
// View
{!! TranslatableBootForm::text('Name', 'name')
                        ->placeholder('My placeholder') !!}

// Output
<div class="form-group form-group-translation">
    <label for="name[en]">Name (en)</label>
    <input type="text" name="name[en]" class="form-control" placeholder="My Placeholder" data-language="en">
</div>
<div class="form-group form-group-translation">
    <label for="name[nl]">Name (nl)</label>
    <input type="text" name="name[nl]" class="form-control" placeholder="My Placeholder" data-language="nl">
</div>

// Controller
public function postEdit($request)
{
    $someModel->save($request->all());
}
```

You can use the `%name` and `%locale` placeholders while specifying parameters. The placeholder will be replaced with the corresponding input name or locale.
This can be useful for two-way data binding libraries such as Angular.js or Vue.js. E.g.
```php
{!! TranslatableBootForm::text('Title', 'title')
                        ->attribute('some-attribute', 'Name: %name')
                        ->attribute('another-attribute', 'Locale: %locale') !!}

// Output
<div class="form-group form-group-translation">
    <label for="title[en]">Title (en)</label>
    <input type="text" name="title[en]" class="form-control" some-attribute="Name: title[en]" another-attribute="Locale: en" data-language="en">
</div>
<div class="form-group form-group-translation">
    <label for="title[nl]">Title (nl)</label>
    <input type="text" name="title[nl]" class="form-control" some-attribute="Name: title[nl]" another-attribute="Locale: nl" data-language="nl">
</div>
```

To render a *form element only for some chosen locales*, explicitly call `renderLocale()` as the final method and pass the locale or an array of locales as the first parameter:
```php
TranslatableBootForm::text('Name', 'name')
                    ->renderLocale('en')
```

If you need to apply a *method only for certain locales*, suffix the method with `ForLocale` and pass the locale or an array of locales as the first parameter:

```php
TranslatableBootForm::text('Name', 'name')
                    ->dataForLocale('en', 'attributeName', 'attributeValue')
                    ->addClassForLocale(['en', 'nl'], 'addedClass')
```

For customizing the locale indicator in the label (and several other settings), please take a look at the configuration file.
