<?php

namespace TypiCMS\LaravelTranslatableBootForms;

use Illuminate\Support\Str;
use TypiCMS\BootForms\BootForm;

class TranslatableBootForm
{
    /**
     * BootForm implementation.
     */
    protected BootForm $form;

    /**
     * Array holding config values.
     */
    protected array $config;

    /**
     * Array of locale keys.
     */
    protected array $locales;

    /**
     * The current element type this class is working on.
     */
    protected string $element = '';

    /**
     * The array of arguments to pass in when creating the element.
     */
    protected array $arguments = [];

    /**
     * A keyed array of method => arguments to call on the created input.
     */
    protected array $methods = [];

    /**
     * Boolean indicating if the element should be cloned with corresponding translation name attributes.
     */
    protected bool $cloneElement = false;

    /**
     * Boolean indicating if the element should have an indication that is it a translation.
     */
    protected bool $translatableIndicator = false;

    /**
     * Array holding the mappable element arguments.
     */
    private array $mappableArguments = [
        'text' => ['label', 'name', 'value'],
        'textarea' => ['label', 'name'],
        'password' => ['label', 'name'],
        'date' => ['label', 'name', 'value'],
        'email' => ['label', 'name', 'value'],
        'file' => ['label', 'name', 'value'],
        'inputGroup' => ['label', 'name', 'value'],
        'radio' => ['label', 'name', 'value'],
        'inlineRadio' => ['label', 'name', 'value'],
        'checkbox' => ['label', 'name'],
        'inlineCheckbox' => ['label', 'name'],
        'select' => ['label', 'name', 'options'],
        'button' => ['label', 'name', 'type'],
        'submit' => ['value', 'type'],
        'hidden' => ['name'],
        'label' => ['label'],
        'open' => [],
        'openHorizontal' => ['columnSizes'],
        'bind' => ['model'],
        'close' => [],
    ];

    /**
     * Array holding the methods to call during element behavior processing.
     */
    private array $elementBehaviors = [
        'text' => ['cloneElement', 'translatableIndicator'],
        'textarea' => ['cloneElement', 'translatableIndicator'],
        'password' => ['cloneElement', 'translatableIndicator'],
        'date' => ['cloneElement', 'translatableIndicator'],
        'email' => ['cloneElement', 'translatableIndicator'],
        'file' => ['cloneElement', 'translatableIndicator'],
        'inputGroup' => ['cloneElement', 'translatableIndicator'],
        'radio' => ['cloneElement', 'translatableIndicator'],
        'inlineRadio' => ['cloneElement', 'translatableIndicator'],
        'checkbox' => ['cloneElement', 'translatableIndicator'],
        'inlineCheckbox' => ['cloneElement', 'translatableIndicator'],
        'select' => ['cloneElement', 'translatableIndicator'],
        'button' => ['cloneElement'],
        'submit' => ['cloneElement'],
        'hidden' => ['cloneElement'],
        'label' => [],
        'open' => [],
        'openHorizontal' => [],
        'close' => [],
    ];

    /**
     * Form constructor.
     */
    public function __construct(object $form)
    {
        $this->form = $form;
        $this->config = config('translatable-bootforms');
    }

    /**
     * Magic __call method.
     */
    public function __call(string $method, array $parameters): string|TranslatableBootForm
    {
        // New translatable form element.
        if (empty($this->element())) {
            $this->element($method);
            $this->arguments($this->mapArguments($parameters));
        } // Calling methods on the translatable form element.
        else {
            $this->addMethod($method, $parameters);
        }

        // Execute bind or close immediately.
        if (in_array($method, ['bind', 'close'])) {
            return $this->render();
        }

        return $this;
    }

    /**
     * Magic __toString method.
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Resets the properties.
     */
    protected function reset(): self
    {
        $this->element = '';
        $this->arguments = [];
        $this->methods = [];
        $this->cloneElement = false;
        $this->translatableIndicator = false;

        return $this;
    }

    /**
     * Get or set the available locales.
     */
    public function locales(array $locales = null): array
    {
        return is_null($locales)
            ? $this->locales
            : ($this->locales = $locales);
    }

    /**
     * Get or set the current element.
     */
    protected function element(?string $element = null): string
    {
        return is_null($element)
            ? $this->element
            : ($this->element = $element);
    }

    /**
     * Get or set the arguments.
     */
    protected function arguments(array $arguments = null): array
    {
        return is_null($arguments)
            ? $this->arguments
            : ($this->arguments = $arguments);
    }

    /**
     * Get or set the methods.
     */
    protected function methods(array $methods = null): array
    {
        return is_null($methods)
            ? $this->methods
            : ($this->methods = $methods);
    }

    /**
     * Get or set the current element.
     */
    protected function cloneElement(?bool $clone = null): bool
    {
        return is_null($clone)
            ? $this->cloneElement
            : ($this->cloneElement = (bool) $clone);
    }

    /**
     * Get or set the translatable indicator boolean.
     */
    protected function translatableIndicator(?bool $add = null): bool
    {
        return is_null($add)
            ? $this->translatableIndicator
            : ($this->translatableIndicator = (bool) $add);
    }

    /**
     * Overwrites an argument.
     */
    protected function overwriteArgument(string $argument, array|string $value): void
    {
        $arguments = $this->arguments();

        $arguments[$argument] = $value;

        $this->arguments($arguments);
    }

    /**
     * Adds a method.
     */
    protected function addMethod(string $name, array|string $parameters): void
    {
        $methods = $this->methods();

        $parameters = is_array($parameters) ? $parameters : [$parameters];

        $methods[] = compact('name', 'parameters');

        $this->methods($methods);
    }

    /**
     * Renders the current translatable form element.
     */
    public function render(): string
    {
        $this->applyElementBehavior();

        $elements = [];

        if ($this->cloneElement()) {
            $originalArguments = $this->arguments();
            $originalMethods = $this->methods();

            $locales = $this->locales();
            // Check if a custom locale set is requested.
            if ($count = func_num_args()) {
                $args = ($count == 1 ? head(func_get_args()) : func_get_args());
                $locales = array_intersect($locales, (array) $args);
            }

            foreach ($locales as $locale) {
                $this->arguments($originalArguments);
                $this->methods($originalMethods);
                $this->overwriteArgument('name', $originalArguments['name'] . '[' . $locale . ']');
                if ($this->translatableIndicator()) {
                    $this->setTranslatableLabelIndicator($locale);
                }
                if (!empty($this->config['form-group-class'])) {
                    $this->addMethod('addGroupClass', str_replace('%locale', $locale, 'form-group-translation'));
                }
                if (!empty($this->config['input-locale-attribute'])) {
                    $this->addMethod('attribute', [$this->config['input-locale-attribute'], $locale]);
                }
                $elements[] = $this->createInput($locale);
            }
        } else {
            $elements[] = $this->createInput();
        }

        $this->reset();

        return implode('', $elements);
    }

    /**
     * Shortcut method for locale-specific rendering.
     */
    public function renderLocale(): string
    {
        return call_user_func_array([$this, 'render'], func_get_args());
    }

    /**
     * Creates an input element using the supplied arguments and methods.
     *
     * @return mixed
     */
    protected function createInput(?string $currentLocale = null)
    {
        // Create element using arguments.
        $element = call_user_func_array([$this->form, $this->element()], array_values($this->arguments()));

        // Elements such as 'bind' do not return renderable stuff and do not accept methods.
        if ($element) {
            // Apply requested methods.
            foreach ($this->methods() as $method) {
                $methodName = $method['name'];
                $methodParameters = $method['parameters'];

                // Check if method is locale-specific.
                if (Str::endsWith($methodName, 'ForLocale')) {
                    $methodName = mb_strstr($methodName, 'ForLocale', true);
                    $locales = array_shift($methodParameters);
                    $locales = is_array($locales) ? $locales : [$locales];
                    if (!is_null($currentLocale) && !in_array($currentLocale, $locales)) {
                        // Method should not be applied for this locale.
                        continue;
                    }
                }

                // Call method.
                if (!empty($methodParameters)) {
                    call_user_func_array([$element, $methodName], $this->replacePlaceholdersRecursively($methodParameters, $currentLocale));
                } else {
                    $element->{$methodName}();
                }
            }
        }

        return $element;
    }

    /**
     * Replaces %name recursively with the proper input name.
     *
     * @param mixed $parameter
     *
     * @return mixed
     */
    protected function replacePlaceholdersRecursively($parameter, string $currentLocale)
    {
        if (is_array($parameter)) {
            foreach ($parameter as $param) {
                $this->replacePlaceholdersRecursively($param, $currentLocale);
            }
        }

        return str_replace(['%name', '%locale'], [$this->arguments()['name'], $currentLocale], $parameter);
    }

    /**
     * Add specific element behavior to the current translatable form element.
     */
    protected function applyElementBehavior(): void
    {
        $behaviors = isset($this->elementBehaviors[$this->element()]) ? $this->elementBehaviors[$this->element()] : [];

        foreach ($behaviors as $behavior) {
            $this->{$behavior}(true);
        }
    }

    /**
     * Maps the form element arguments to their name.
     */
    protected function mapArguments(array $arguments): array
    {
        $keys = isset($this->mappableArguments[$this->element()]) ? $this->mappableArguments[$this->element()] : [];

        return array_combine(array_slice($keys, 0, count($arguments)), $arguments);
    }

    /**
     * Add a locale indicator to the label.
     */
    protected function setTranslatableLabelIndicator(string $locale): void
    {
        $localizedLabel = str_replace('%label', $this->arguments()['label'], $this->config['label-locale-indicator']);
        $this->overwriteArgument('label', str_replace('%locale', $locale, $localizedLabel));
    }
}
