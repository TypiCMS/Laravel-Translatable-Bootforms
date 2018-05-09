<?php

namespace TypiCMS\LaravelTranslatableBootForms\Tests;

use Illuminate\Database\Capsule\Manager as DB;
use Orchestra\Testbench\TestCase;
use TypiCMS\BootForms\BootFormsServiceProvider;
use TypiCMS\BootForms\Facades\BootForm;
use TypiCMS\LaravelTranslatableBootForms\Facades\TranslatableBootForm;
use TypiCMS\LaravelTranslatableBootForms\Tests\Models\Model;
use TypiCMS\LaravelTranslatableBootForms\TranslatableBootFormsServiceProvider;

class TestTranslatableBootForms extends TestCase
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            BootFormsServiceProvider::class,
            TranslatableBootFormsServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'BootForm' => BootForm::class,
            'TranslatableBootForm' => TranslatableBootForm::class,
        ];
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('translatable-bootforms.locales', ['en', 'nl']);
        $this->app['config']->set('translatable-bootforms.label-locale-indicator', '%label (%locale)');

        $this->bootform = $this->app->make('bootform');
        $this->form = $this->app->make('translatable-bootform');
    }

    /**
     * Setup the test database.
     *
     * @return void
     */
    protected function configureDatabase()
    {
        $db = new DB();
        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
        $db->bootEloquent();
        $db->setAsGlobal();
        $this->migrateTable();
    }

    /**
     * Setup the test table.
     *
     * @return void
     */
    protected function migrateTable()
    {
        DB::schema()->create('models', function ($table) {
            $table->increments('id');
            $table->string('default');
            $table->text('input');
        });

        Model::create(['default' => 'model', 'input' => '{"en":"translation","nl":"vertaling"}']);
    }

    public function testBootformFormbuilderIsSharedWithTranslatableBootform()
    {
        $this->bootform->open()->render();

        $reflectionForm = new \ReflectionClass($this->form);
        $formProperty = $reflectionForm->getProperty('form');
        $formProperty->setAccessible(true);
        $bootform = $formProperty->getValue($this->form);

        $reflectionBootform = new \ReflectionClass($bootform);
        $builder = $reflectionBootform->getProperty('builder');
        $builder->setAccessible(true);

        $this->assertNotNull($builder->getValue($bootform));
    }

    public function testTranslatableBootformFormbuilderIsSharedWithBootform()
    {
        $this->form->open()->render();

        $reflectionForm = new \ReflectionClass($this->bootform);
        $builder = $reflectionForm->getProperty('builder');
        $builder->setAccessible(true);

        $this->assertNotNull($builder->getValue($this->bootform));
    }

    public function testBootformBoundModelIsSharedWithTranslatableBootform()
    {
        $this->configureDatabase();

        $this->form->open()->render();

        $this->bootform->bind(Model::find(1));

        $reflectionForm = new \ReflectionClass($this->form);
        $formProperty = $reflectionForm->getProperty('form');
        $formProperty->setAccessible(true);
        $bootform = $formProperty->getValue($this->form);

        $reflectionBootform = new \ReflectionClass($bootform);
        $builderProperty = $reflectionBootform->getProperty('builder');
        $builderProperty->setAccessible(true);
        $builder = $builderProperty->getValue($bootform);

        $reflectionBuilder = new \ReflectionClass($builder);
        $formBuilderProperty = $reflectionBuilder->getProperty('builder');
        $formBuilderProperty->setAccessible(true);
        $formBuilder = $formBuilderProperty->getValue($builder);

        $reflectionFormbuilder = new \ReflectionClass($formBuilder);
        $modelProperty = $reflectionFormbuilder->getProperty('boundData');
        $modelProperty->setAccessible(true);
        $model = $modelProperty->getValue($formBuilder);

        $this->assertNotNull($model);
    }

    public function testTranslatableBootformBoundModelIsSharedWithBootform()
    {
        $this->configureDatabase();

        $this->bootform->open()->render();

        $this->form->bind(Model::find(1));

        $reflectionBootform = new \ReflectionClass($this->bootform);
        $builderProperty = $reflectionBootform->getProperty('builder');
        $builderProperty->setAccessible(true);
        $builder = $builderProperty->getValue($this->bootform);

        $reflectionBuilder = new \ReflectionClass($builder);
        $formBuilderProperty = $reflectionBuilder->getProperty('builder');
        $formBuilderProperty->setAccessible(true);
        $formBuilder = $formBuilderProperty->getValue($builder);

        $reflectionFormbuilder = new \ReflectionClass($formBuilder);
        $modelProperty = $reflectionFormbuilder->getProperty('boundData');
        $modelProperty->setAccessible(true);
        $model = $modelProperty->getValue($formBuilder);

        $this->assertNotNull($model);
    }

    public function testBootformCloseIsSharedWithTranslatableBootform()
    {
        $this->configureDatabase();

        $this->form->open()->render();

        $this->bootform->bind(Model::find(1));

        $this->bootform->close();

        $reflectionForm = new \ReflectionClass($this->form);
        $formProperty = $reflectionForm->getProperty('form');
        $formProperty->setAccessible(true);
        $bootform = $formProperty->getValue($this->form);

        $reflectionBootform = new \ReflectionClass($bootform);
        $builderProperty = $reflectionBootform->getProperty('builder');
        $builderProperty->setAccessible(true);
        $builder = $builderProperty->getValue($bootform);

        $reflectionBuilder = new \ReflectionClass($builder);
        $formBuilderProperty = $reflectionBuilder->getProperty('builder');
        $formBuilderProperty->setAccessible(true);
        $formBuilder = $formBuilderProperty->getValue($builder);

        $reflectionFormbuilder = new \ReflectionClass($formBuilder);
        $modelProperty = $reflectionFormbuilder->getProperty('boundData');
        $modelProperty->setAccessible(true);
        $model = $modelProperty->getValue($formBuilder);

        $this->assertNull($model);
    }

    public function testTranslatableBootformCloseIsSharedWithBootform()
    {
        $this->configureDatabase();

        $this->bootform->open()->render();

        $this->form->bind(Model::find(1));

        $this->form->close();

        $reflectionBootform = new \ReflectionClass($this->bootform);
        $builderProperty = $reflectionBootform->getProperty('builder');
        $builderProperty->setAccessible(true);
        $builder = $builderProperty->getValue($this->bootform);

        $reflectionBuilder = new \ReflectionClass($builder);
        $formBuilderProperty = $reflectionBuilder->getProperty('builder');
        $formBuilderProperty->setAccessible(true);
        $formBuilder = $formBuilderProperty->getValue($builder);

        $reflectionFormbuilder = new \ReflectionClass($formBuilder);
        $modelProperty = $reflectionFormbuilder->getProperty('boundData');
        $modelProperty->setAccessible(true);
        $model = $modelProperty->getValue($formBuilder);

        $this->assertNull($model);
    }

    public function testRenderTranslatableInput()
    {
        $this->form->open()->render();

        $expected = '<div class="form-group form-group-translation"><label for="input[en]">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" data-language="en"></div><div class="form-group form-group-translation"><label for="input[nl]">Input (nl)</label><input type="text" name="input[nl]" id="input[nl]" class="form-control" data-language="nl"></div>';
        $result = $this->form->text('Input', 'input')->render();
        $this->assertEquals($expected, $result);
    }

    public function testRenderTranslatableInputWithMethods()
    {
        $this->form->open()->render();

        $expected = '<div class="form-group form-group-translation"><label for="input[en]" class="newClass control-label-required">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" data-test="ok" required="required" data-language="en"></div><div class="form-group form-group-translation"><label for="input[nl]" class="newClass control-label-required">Input (nl)</label><input type="text" name="input[nl]" id="input[nl]" class="form-control" data-test="ok" required="required" data-language="nl"></div>';
        $result = $this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render();
        $this->assertEquals($expected, $result);
    }

    public function testRenderTranslatableInputWithLocaleSpecificMethods()
    {
        $this->form->open()->render();

        $expected = '<div class="form-group form-group-translation"><label for="input[en]" class="newClass control-label-required">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" required="required" data-all="test" data-language="en"></div><div class="form-group form-group-translation"><label for="input[nl]" class="newClass">Input (nl)</label><input type="text" name="input[nl]" id="input[nl]" class="form-control" data-test="ok" data-all="test" data-language="nl"></div>';
        $result = $this->form->text('Input', 'input')->dataForLocale('nl', 'test', 'ok')->labelClassForLocale(['nl', 'en'], 'newClass')->requiredForLocale('en')->data('all', 'test')->render();
        $this->assertEquals($expected, $result);
    }

    public function testRenderTranslatableInputWhenCastedToString()
    {
        $this->form->open()->render();

        $expected = '<div class="form-group form-group-translation"><label for="input[en]">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" data-language="en"></div><div class="form-group form-group-translation"><label for="input[nl]">Input (nl)</label><input type="text" name="input[nl]" id="input[nl]" class="form-control" data-language="nl"></div>';
        $result = (string) $this->form->text('Input', 'input');
        $this->assertEquals($expected, $result);
    }

    public function testRenderTranslatableInputWithCustomRequestedLocales()
    {
        $this->form->open()->render();

        $expected = '<div class="form-group form-group-translation"><label for="input[en]" class="newClass control-label-required">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" data-test="ok" required="required" data-language="en"></div>';
        $result = $this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render('en');
        $this->assertEquals($expected, $result);

        $expected = '<div class="form-group form-group-translation"><label for="input[en]" class="newClass control-label-required">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" data-test="ok" required="required" data-language="en"></div><div class="form-group form-group-translation"><label for="input[nl]" class="newClass control-label-required">Input (nl)</label><input type="text" name="input[nl]" id="input[nl]" class="form-control" data-test="ok" required="required" data-language="nl"></div>';
        $result = $this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render('en', 'nl');
        $this->assertEquals($expected, $result);

        $expected = '<div class="form-group form-group-translation"><label for="input[en]" class="newClass control-label-required">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" data-test="ok" required="required" data-language="en"></div>';
        $result = $this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render(['en']);
        $this->assertEquals($expected, $result);

        $expected = '<div class="form-group form-group-translation"><label for="input[en]" class="newClass control-label-required">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" data-test="ok" required="required" data-language="en"></div><div class="form-group form-group-translation"><label for="input[nl]" class="newClass control-label-required">Input (nl)</label><input type="text" name="input[nl]" id="input[nl]" class="form-control" data-test="ok" required="required" data-language="nl"></div>';
        $result = $this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->render(['en', 'nl']);
        $this->assertEquals($expected, $result);

        $expected = '<div class="form-group form-group-translation"><label for="input[en]" class="newClass control-label-required">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" data-test="ok" required="required" data-language="en"></div>';
        $result = $this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->renderLocale('en');
        $this->assertEquals($expected, $result);

        $expected = '<div class="form-group form-group-translation"><label for="input[en]" class="newClass control-label-required">Input (en)</label><input type="text" name="input[en]" id="input[en]" class="form-control" data-test="ok" required="required" data-language="en"></div><div class="form-group form-group-translation"><label for="input[nl]" class="newClass control-label-required">Input (nl)</label><input type="text" name="input[nl]" id="input[nl]" class="form-control" data-test="ok" required="required" data-language="nl"></div>';
        $result = $this->form->text('Input', 'input')->data('test', 'ok')->labelClass('newClass')->required()->renderLocale(['en', 'nl']);
        $this->assertEquals($expected, $result);
    }

    public function testBootformStillGetsRegularValues()
    {
        $this->configureDatabase();

        $this->form->open()->render();

        $this->form->bind(Model::find(1));

        $expected = '<div class="form-group"><label for="default">Input</label><input type="text" name="default" value="model" id="default" class="form-control"></div>';
        $result = $this->bootform->text('Input', 'default')->render();
        $this->assertEquals($expected, $result);
    }

    public function testGetTranslatedModelValues()
    {
        $this->configureDatabase();

        $this->form->open()->render();

        $this->form->bind(Model::find(1));

        $expected = '<div class="form-group form-group-translation"><label for="input[en]">Input (en)</label><input type="text" name="input[en]" value="translation" id="input[en]" class="form-control" data-language="en"></div><div class="form-group form-group-translation"><label for="input[nl]">Input (nl)</label><input type="text" name="input[nl]" value="vertaling" id="input[nl]" class="form-control" data-language="nl"></div>';
        $result = $this->form->text('Input', 'input')->render();
        $this->assertEquals($expected, $result);
    }
}
