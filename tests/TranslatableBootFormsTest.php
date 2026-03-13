<?php

declare(strict_types=1);

use TypiCMS\LaravelTranslatableBootForms\Tests\Models\Model;

it('shares bootform formbuilder with translatable bootform', function (): void {
    $this->bootform->open()->render();

    $reflectionForm = new ReflectionClass($this->form);
    $reflectionProperty = $reflectionForm->getProperty('bootForm');

    $bootform = $reflectionProperty->getValue($this->form);

    $reflectionBootform = new ReflectionClass($bootform);
    $builder = $reflectionBootform->getProperty('builder');

    expect($builder->getValue($bootform))->not->toBeNull();
});

it('shares translatable bootform formbuilder with bootform', function (): void {
    $this->form->open()->render();

    $reflectionForm = new ReflectionClass($this->bootform);
    $reflectionProperty = $reflectionForm->getProperty('builder');

    expect($reflectionProperty->getValue($this->bootform))->not->toBeNull();
});

it('shares bootform bound model with translatable bootform', function (): void {
    configureDatabase();

    $this->form->open()->render();
    $this->bootform->bind(Model::find(1));

    $reflectionForm = new ReflectionClass($this->form);
    $reflectionProperty = $reflectionForm->getProperty('bootForm');

    $bootform = $reflectionProperty->getValue($this->form);

    $reflectionBootform = new ReflectionClass($bootform);
    $builderProperty = $reflectionBootform->getProperty('builder');

    $builder = $builderProperty->getValue($bootform);

    $reflectionBuilder = new ReflectionClass($builder);
    $formBuilderProperty = $reflectionBuilder->getProperty('builder');

    $formBuilder = $formBuilderProperty->getValue($builder);

    $reflectionFormbuilder = new ReflectionClass($formBuilder);
    $modelProperty = $reflectionFormbuilder->getProperty('boundData');

    expect($modelProperty->getValue($formBuilder))->not->toBeNull();
});

it('shares translatable bootform bound model with bootform', function (): void {
    configureDatabase();

    $this->bootform->open()->render();
    $this->form->bind(Model::find(1));

    $reflectionBootform = new ReflectionClass($this->bootform);
    $reflectionProperty = $reflectionBootform->getProperty('builder');

    $builder = $reflectionProperty->getValue($this->bootform);

    $reflectionBuilder = new ReflectionClass($builder);
    $formBuilderProperty = $reflectionBuilder->getProperty('builder');

    $formBuilder = $formBuilderProperty->getValue($builder);

    $reflectionFormbuilder = new ReflectionClass($formBuilder);
    $modelProperty = $reflectionFormbuilder->getProperty('boundData');

    expect($modelProperty->getValue($formBuilder))->not->toBeNull();
});

it('shares bootform close with translatable bootform', function (): void {
    configureDatabase();

    $this->form->open()->render();
    $this->bootform->bind(Model::find(1));
    $this->bootform->close();

    $reflectionForm = new ReflectionClass($this->form);
    $reflectionProperty = $reflectionForm->getProperty('bootForm');

    $bootform = $reflectionProperty->getValue($this->form);

    $reflectionBootform = new ReflectionClass($bootform);
    $builderProperty = $reflectionBootform->getProperty('builder');

    $builder = $builderProperty->getValue($bootform);

    $reflectionBuilder = new ReflectionClass($builder);
    $formBuilderProperty = $reflectionBuilder->getProperty('builder');

    $formBuilder = $formBuilderProperty->getValue($builder);

    $reflectionFormbuilder = new ReflectionClass($formBuilder);
    $modelProperty = $reflectionFormbuilder->getProperty('boundData');

    expect($modelProperty->getValue($formBuilder))->toBeNull();
});

it('shares translatable bootform close with bootform', function (): void {
    configureDatabase();

    $this->bootform->open()->render();
    $this->form->bind(Model::find(1));
    $this->form->close();

    $reflectionBootform = new ReflectionClass($this->bootform);
    $reflectionProperty = $reflectionBootform->getProperty('builder');

    $builder = $reflectionProperty->getValue($this->bootform);

    $reflectionBuilder = new ReflectionClass($builder);
    $formBuilderProperty = $reflectionBuilder->getProperty('builder');

    $formBuilder = $formBuilderProperty->getValue($builder);

    $reflectionFormbuilder = new ReflectionClass($formBuilder);
    $modelProperty = $reflectionFormbuilder->getProperty('boundData');

    expect($modelProperty->getValue($formBuilder))->toBeNull();
});

it('renders translatable input', function (): void {
    $this->form->open()->render();

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" data-language="en"></div><div class="mb-3 form-group-translation"><label for="input[nl]" class="form-label">Input (nl)</label><input type="text" name="input[nl]" class="form-control" id="input[nl]" data-language="nl"></div>';

    expect($this->form->text('Input', 'input')->render())->toBe($expected);
});

it('renders translatable input with methods', function (): void {
    $this->form->open()->render();

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label newClass form-label-required">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" data-test="ok" required="required" data-language="en"></div><div class="mb-3 form-group-translation"><label for="input[nl]" class="form-label newClass form-label-required">Input (nl)</label><input type="text" name="input[nl]" class="form-control" id="input[nl]" data-test="ok" required="required" data-language="nl"></div>';

    expect($this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render())->toBe($expected);
});

it('renders translatable input with locale specific methods', function (): void {
    $this->form->open()->render();

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label newClass form-label-required">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" required="required" data-all="test" data-language="en"></div><div class="mb-3 form-group-translation"><label for="input[nl]" class="form-label newClass">Input (nl)</label><input type="text" name="input[nl]" class="form-control" id="input[nl]" data-test="ok" data-all="test" data-language="nl"></div>';

    expect($this->form->text('Input', 'input')->dataForLocale('nl', 'test', 'ok')->labelClassForLocale(['nl', 'en'], 'newClass')->requiredForLocale('en')->data('all', 'test')->render())->toBe($expected);
});

it('renders translatable input when casted to string', function (): void {
    $this->form->open()->render();

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" data-language="en"></div><div class="mb-3 form-group-translation"><label for="input[nl]" class="form-label">Input (nl)</label><input type="text" name="input[nl]" class="form-control" id="input[nl]" data-language="nl"></div>';

    expect((string) $this->form->text('Input', 'input'))->toBe($expected);
});

it('renders translatable input with custom requested locales', function (): void {
    $this->form->open()->render();

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label newClass form-label-required">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" data-test="ok" required="required" data-language="en"></div>';
    expect($this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render('en'))->toBe($expected);

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label newClass form-label-required">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" data-test="ok" required="required" data-language="en"></div><div class="mb-3 form-group-translation"><label for="input[nl]" class="form-label newClass form-label-required">Input (nl)</label><input type="text" name="input[nl]" class="form-control" id="input[nl]" data-test="ok" required="required" data-language="nl"></div>';
    expect($this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render('en', 'nl'))->toBe($expected);

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label newClass form-label-required">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" data-test="ok" required="required" data-language="en"></div>';
    expect($this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render(['en']))->toBe($expected);

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label newClass form-label-required">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" data-test="ok" required="required" data-language="en"></div><div class="mb-3 form-group-translation"><label for="input[nl]" class="form-label newClass form-label-required">Input (nl)</label><input type="text" name="input[nl]" class="form-control" id="input[nl]" data-test="ok" required="required" data-language="nl"></div>';
    expect($this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render(['en', 'nl']))->toBe($expected);

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label newClass form-label-required">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" data-test="ok" required="required" data-language="en"></div>';
    expect($this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->renderLocale('en'))->toBe($expected);

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label newClass form-label-required">Input (en)</label><input type="text" name="input[en]" class="form-control" id="input[en]" data-test="ok" required="required" data-language="en"></div><div class="mb-3 form-group-translation"><label for="input[nl]" class="form-label newClass form-label-required">Input (nl)</label><input type="text" name="input[nl]" class="form-control" id="input[nl]" data-test="ok" required="required" data-language="nl"></div>';
    expect($this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->renderLocale(['en', 'nl']))->toBe($expected);
});

it('still gets regular values via bootform', function (): void {
    configureDatabase();

    $this->form->open()->render();
    $this->form->bind(Model::find(1));

    $expected = '<div class="mb-3"><label for="default" class="form-label">Input</label><input type="text" name="default" value="model" class="form-control" id="default"></div>';

    expect($this->bootform->text('Input', 'default')->render())->toBe($expected);
});

it('gets translated model values', function (): void {
    configureDatabase();

    $this->form->open()->render();
    $this->form->bind(Model::find(1));

    $expected = '<div class="mb-3 form-group-translation"><label for="input[en]" class="form-label">Input (en)</label><input type="text" name="input[en]" value="translation" class="form-control" id="input[en]" data-language="en"></div><div class="mb-3 form-group-translation"><label for="input[nl]" class="form-label">Input (nl)</label><input type="text" name="input[nl]" value="vertaling" class="form-control" id="input[nl]" data-language="nl"></div>';

    expect($this->form->text('Input', 'input')->render())->toBe($expected);
});
