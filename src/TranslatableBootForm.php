<?php namespace Propaganistas\LaravelTranslatableBootForms;

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
     * Array holding the mappable element arguments.
     *
     * @var array
     */
    private $mappableArguments = [
        'text'           => ['label', 'name', 'value'],
        'textarea'       => ['label', 'name'],
        'password'       => ['label', 'name'],
        'date'           => ['label', 'name', 'value'],
        'email'          => ['label', 'name', 'value'],
        'file'           => ['label', 'name', 'value'],
        'inputGroup'     => ['label', 'name', 'value'],
        'radio'          => ['label', 'name', 'value'],
        'inlineRadio'    => ['label', 'name', 'value'],
        'checkbox'       => ['label', 'name'],
        'inlineCheckbox' => ['label', 'name'],
        'select'         => ['label', 'name', 'options'],
        'button'         => ['label', 'name', 'type'],
        'submit'         => ['value', 'type'],
        'hidden'         => ['name'],
        'label'          => ['label'],
        'open'           => [],
        'openHorizontal' => ['columnSizes'],
        'bind'           => ['model'],
        'close'          => [],
    ];

    /**
     * Array holding the methods to call during element behavior processing.
     *
     * @var array
     */
    private $elementBehaviors = [
        'text'           => ['cloneElement', 'translatableIndicator'],
        'textarea'       => ['cloneElement', 'translatableIndicator'],
        'password'       => ['cloneElement', 'translatableIndicator'],
        'date'           => ['cloneElement', 'translatableIndicator'],
        'email'          => ['cloneElement', 'translatableIndicator'],
        'file'           => ['cloneElement', 'translatableIndicator'],
        'inputGroup'     => ['cloneElement', 'translatableIndicator'],
        'radio'          => ['cloneElement', 'translatableIndicator'],
        'inlineRadio'    => ['cloneElement', 'translatableIndicator'],
        'checkbox'       => ['cloneElement', 'translatableIndicator'],
        'inlineCheckbox' => ['cloneElement', 'translatableIndicator'],
        'select'         => ['cloneElement', 'translatableIndicator'],
        'button'         => ['cloneElement'],
        'submit'         => ['cloneElement'],
        'hidden'         => ['cloneElement'],
        'label'          => [],
        'open'           => [],
        'openHorizontal' => [],
        'close'          => [],
    ];

    /**
     * Form constructor.
     *
     * @param object $form
     */
    public function __construct($form)
    {
        $this->form = $form;
        $this->config = config('translatable-bootforms');
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
    protected function cloneElement($clone = null)
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
    protected function translatableIndicator($add = null)
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

        $parameters = is_array($parameters) ? $parameters : [$parameters];

        $methods[] = compact('name', 'parameters');

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
                $this->overwriteArgument('name', $locale . '[' . $originalArguments['name'] . ']');
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
     *
     * @return string
     */
    public function renderLocale()
    {
        return call_user_func_array([$this, 'render'], func_get_args());
    }

    /**
     * Creates an input element using the supplied arguments and methods.
     *
     * @param string|null $currentLocale
     * @return mixed
     */
    protected function createInput($currentLocale = null)
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
                if (ends_with($methodName, 'ForLocale')) {
                    $methodName = strstr($methodName, 'ForLocale', true);
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
     * @param string $currentLocale
     * @return mixed
     */
    protected function replacePlaceholdersRecursively($parameter, $currentLocale)
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
    protected function applyElementBehavior()
    {
        $behaviors = isset($this->elementBehaviors[$this->element()]) ? $this->elementBehaviors[$this->element()] : [];

        foreach ($behaviors as $behavior) {
            $this->{$behavior}(true);
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
        $keys = isset($this->mappableArguments[$this->element()]) ? $this->mappableArguments[$this->element()] : [];

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
