<?php namespace Propaganistas\LaravelTranslatableBootForms\Form;

use AdamWathan\Form\Binding\BoundData;
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
     * Sets the available locales for translatable fields.
     *
     * @param array $locales
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
     * Courtesy of TypiCMS/TranslatableBootForms (https://github.com/TypiCMS/TranslatableBootForms/blob/master/src/TranslatableFormBuilder.php)
     *
     * @param string $name key
     * @return string value
     */
    protected function getBoundValue($name, $default)
    {
        $inputName = preg_split('/[\[\]]+/', $name, - 1, PREG_SPLIT_NO_EMPTY);
        if (config('app.translator') === 'spatie') {
            if (count($inputName) == 2 && in_array($inputName[1], $this->locales)) {
                list($name, $lang) = $inputName;
                $translation = $this->boundData->data()->getTranslation($name, $lang);
                $value = $translation ? : '';

                return $this->escape($value);
            }
        } else {
            if (count($inputName) == 2 && in_array($inputName[0], $this->locales)) {
                list($lang, $name) = $inputName;
                $value = isset($this->boundData->data()->translate($lang)->{$name})
                    ? $this->boundData->data()->translate($lang)->{$name}
                    : '';

                return $this->escape($value);
            }
        }

        return $this->escape($this->boundData->get($name, $default));
    }
}
