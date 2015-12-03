<?php namespace Propaganistas\LaravelTranslatableBootForms\Form;

use AdamWathan\Form\Elements\Label;
use AdamWathan\Form\FormBuilder as _FormBuilder;

class FormBuilder extends _FormBuilder
{

    /**
     * Array of locale keys.
     *
     * @var array
     */
    protected $locales;

    /**
     * Since $model is a private property in the parent class, we need to define the model in this child class as well.
     * {@inheritdoc}
     */
    protected $model;

    /**
     * Sets the available locales for translatable fields.
     *
     * @param array $locales
     */
    public function setLocales(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * Since $model is a private property in the parent class, we need to bind the model in this child class as well.
     * {@inheritdoc}
     *
     */
    public function bind($model)
    {
        $this->model = is_array($model) ? (object) $model : $model;
        parent::bind($model);
    }

    /**
     * Since $model is a private property in the parent class, we need to unbind the model in this child class as well.
     * {@inheritdoc}
     *
     */
    protected function unbindModel()
    {
        $this->model = null;
        parent::unbindModel();
    }

    /**
     * Getting value from Model or ModelTranslation to populate form.
     * Courtesy of TypiCMS/TranslatableBootForms (https://github.com/TypiCMS/TranslatableBootForms/blob/master/src/TranslatableFormBuilder.php)
     *
     * @param string $name key
     * @return string value
     */
    protected function getModelValue($name)
    {
        $inputName = preg_split('/[\[\]]+/', $name, - 1, PREG_SPLIT_NO_EMPTY);
        if (count($inputName) == 2 && in_array($inputName[0], $this->locales)) {
            list($lang, $name) = $inputName;
            $value = isset($this->model->translate($lang)->{$name})
                ? $this->model->translate($lang)->{$name}
                : '';

            return $this->escape($value);
        }

        return $this->escape($this->model->{$name});
    }

    /**
     * Create a label.
     *
     * @param string $label
     * @return \AdamWathan\Form\Elements\Label
     */
    public function label($label)
    {
        // Wrap the label title in span.
        if (strpos($label, '<span') !== 0) {
            $length = strpos($label, '<') ?: strlen($label);
            $label_title = substr($label, 0, $length);
            $label = str_replace($label_title, '<span>' . $label_title . '</span>', $label);
        }

        return new Label($label);
    }
}