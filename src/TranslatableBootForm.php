<?php namespace Propaganistas\LaravelTranslatableBootForms;

use AdamWathan\BootForms\BootForm;

class TranslatableBootForm
{

    /**
     * BootForm implementation.
     *
     * @var \AdamWathan\BootForms\BootForm
     */
    protected $form;

    /**
     * Array holding config values.
     *
     * @var array
     */
    protected $config;

    /**
     * Array of locale keys.
     *
     * @var array
     */
    protected $locales;

    /**
     * The current element type this class is working on.
     *
     * @var string
     */
    protected $element;

    /**
     * The array of arguments to pass in when creating the element.
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * A keyed array of method => arguments to call on the created input.
     *
     * @var array
     */
    protected $methods = [];

    /**
     * Boolean indicating if the element should be cloned with corresponding translation name attributes.
     *
     * @var bool
     */
    protected $cloneElement = false;

    /**
     * Boolean indicating if the element should have an indication that is it a translation.
     *
     * @var bool
     */
    protected $translatableIndicator = false;

    /**
     * Form constructor.
     *
     * @param \AdamWathan\BootForms\BootForm $form
     */
    public function __construct(BootForm $form)
    {
        $this->form = $form;
        $this->config = [
            'form-group-class' => config('translatable-bootforms::form-group-class'),
            'input-locale-attribute' => config('translatable-bootforms::input-locale-attribute'),
            'label-locale-indicator' => config('translatable-bootforms::label-locale-indicator'),
        ];
    }

    /**
     * Magic __call method.
     *
     * @param string $method
     * @param array  $parameters
     * @return \Propaganistas\LaravelTranslatableBootForms\TranslatableBootForm
     */
    public function __call($method, $parameters)
    {
        // New translatable form element.
        if (is_null($this->element())) {
            $method = camel_case(substr($method, 12));
            $this->element($method);
            $this->arguments($this->mapArguments($parameters));
        }
        // Calling methods on the translatable form element.
        else {
            $this->addMethod($method, $parameters);
        }

        return $this;
    }

    /**
     * Magic __toString method.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Resets the properties.
     *
     * @return $this
     */
    protected function reset()
    {
        $this->element = null;
        $this->arguments = [];
        $this->methods = [];
        $this->cloneElement = false;
        $this->translatableIndicator = false;

        return $this;
    }

    /**
     * Get or set the available locales.
     *
     * @param array|null $locales
     * @return array
     */
    public function locales(array $locales = null)
    {
        return is_null($locales)
            ? $this->locales
            : ($this->locales = $locales);
    }

    /**
     * Get or set the current element.
     *
     * @param string|null $element
     * @return string
     */
    protected function element($element = null)
    {
        return is_null($element)
            ? $this->element
            : ($this->element = $element);
    }

    /**
     * Get or set the arguments.
     *
     * @param array|null $arguments
     * @return array
     */
    protected function arguments(array $arguments = null)
    {
        return is_null($arguments)
            ? $this->arguments
            : ($this->arguments = $arguments);
    }

    /**
     * Get or set the methods.
     *
     * @param array|null $methods
     * @return array
     */
    protected function methods(array $methods = null)
    {
        return is_null($methods)
            ? $this->methods
            : ($this->methods = $methods);
    }

    /**
     * Get or set the current element.
     *
     * @param bool|null $clone
     * @return bool
     */
    protected function cloneElement(bool $clone = null)
    {
        return is_null($clone)
            ? $this->cloneElement
            : ($this->cloneElement = (bool) $clone);
    }

    /**
     * Get or set the translatable indicator boolean.
     *
     * @param bool|null $add
     * @return bool
     */
    protected function translatableIndicator(bool $add = null)
    {
        return is_null($add)
            ? $this->translatableIndicator
            : ($this->translatableIndicator = (bool) $add);
    }

    /**
     * Overwrites an argument.
     *
     * @param string       $argument
     * @param string|array $value
     */
    protected function overwriteArgument($argument, $value)
    {
        $arguments = $this->arguments();

        $arguments[$argument] = $value;

        $this->arguments($arguments);
    }

    /**
     * Adds a method.
     *
     * @param string       $name
     * @param string|array $parameters
     */
    protected function addMethod($name, $parameters)
    {
        $methods = $this->methods();

        $methods[] = compact('name','parameters');

        $this->methods($methods);
    }

    /**
     * Renders the current translatable form element.
     *
     * @return string
     */
    public function render()
    {
        $this->applyElementBehavior();

        $elements = [];

        if ($this->cloneElement()) {
            $this->addMethod('addGroupClass', $this->config['form-group-class']);

            $originalArguments = $this->arguments();
            $originalMethods = $this->methods();

            foreach ($this->locales() as $locale => $language) {
                $this->arguments($originalArguments);
                $this->methods($originalMethods);
                $this->overwriteArgument('name', $locale . '[' . $originalArguments['name'] . ']');
                if ($this->translatableIndicator()) {
                    $this->setTranslatableLabelIndicator($locale);
                }
                $this->addMethod('attribute', [$this->config['input-locale-attribute'], $locale]);
                $elements[] = $this->createInput();
            }
        } else {
            $elements[] = $this->createInput();
        }

        $this->reset();

        return implode('', $elements);
    }

    /**
     * Creates an input element using the supplied arguments and methods.
     *
     * @return mixed
     */
    protected function createInput()
    {
        // Create element using arguments.
        $element = $this->form->{$this->element()}(...array_values($this->arguments()));

        // Apply requested methods.
        foreach ($this->methods() as $method) {
            $methodName = $method['name'];
            $methodParameters = $method['parameters'];
            if (is_array($methodParameters)) {
                $element->{$methodName}(...$methodParameters);
            } elseif (!empty($methodParameters)) {
                $element->{$methodName}($methodParameters);
            } else {
                $element->{$methodName}();
            }

        }

        return $element;
    }

    /**
     * Add specific element behavior to the current translatable form element.
     */
    protected function applyElementBehavior()
    {
        switch ($this->element()) {
            case 'text':
            case 'textarea':
            case 'password':
            case 'date':
            case 'email':
            case 'file':
            case 'radio':
            case 'inlineRadio':
            case 'checkbox':
            case 'inlineCheckbox':
            case 'select':
            case 'inputGroup':
                $this->cloneElement(true);
                $this->translatableIndicator(true);
                break;
            case 'hidden':
            case 'button':
            case 'submit':
                $this->cloneElement(true);
                break;
            case 'open':
            case 'openHorizontal':
            case 'close':
            default:
                break;
        }
    }

    /**
     * Maps the form element arguments to their name.
     *
     * @param array $arguments
     * @return array
     */
    protected function mapArguments(array $arguments)
    {
        switch ($this->element()) {
            case 'text':
            case 'textarea':
            case 'password':
            case 'date':
            case 'email':
            case 'file':
            case 'inputGroup':
            case 'radio':
            case 'inlineRadio':
            case 'checkbox':
            case 'inlineCheckbox':
                $keys = ['label', 'name'];
                break;
            case 'select':
                $keys = ['label', 'name', 'options'];
                break;
            case 'button':
                $keys = ['value', 'name', 'type'];
                break;
            case 'submit':
                $keys = ['value', 'type'];
                break;
            case 'hidden':
                $keys = ['name'];
                break;
            case 'label':
                $keys = ['label'];
                break;
            case 'openHorizontal':
                $keys = ['columnSizes'];
                break;
            case 'open':
            case 'close':
            default:
                return [];
        }

        return array_combine(array_slice($keys, 0, count($arguments)), $arguments);
    }

    /**
     * Add a locale indicator to the label.
     *
     * @param string $locale
     */
    protected function setTranslatableLabelIndicator($locale)
    {
        $localizedLabel = str_replace('%label', $this->arguments()['label'], $this->config['label-locale-indicator']);
        $this->overwriteArgument('label', str_replace('%locale', $locale, $localizedLabel));
    }

}