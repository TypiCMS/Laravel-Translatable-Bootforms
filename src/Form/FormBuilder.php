<?php

namespace TypiCMS\LaravelTranslatableBootForms\Form;

use TypiCMS\Form\Binding\BoundData;
use TypiCMS\Form\FormBuilder as BaseFormBuilder;

class FormBuilder extends BaseFormBuilder
{
    /**
     * Array of locale keys.
     *
     * @var array
     */
    protected $locales;

    /**
     * Sets the available locales for translatable fields.
     */
    public function setLocales(array $locales)
    {
        $this->locales = $locales;
    }

    public function bind($data)
    {
        $this->boundData = new BoundData($data);
    }

    /**
     * Getting value from Model or ModelTranslation to populate form.
     *
     * @param string $name    key
     * @param mixed  $default
     *
     * @return string value
     */
    protected function getBoundValue($name, $default)
    {
        $inputName = preg_split('/[\[\]]+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        if (count($inputName) == 2 && in_array($inputName[1], $this->locales)) {
            list($name, $lang) = $inputName;
            $translation = $this->boundData->data()->getTranslation($name, $lang);
            $value = $translation ?: '';

            return $value;
        }

        return $this->boundData->get($name, $default);
    }
}
