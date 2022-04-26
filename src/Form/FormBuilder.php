<?php

namespace TypiCMS\LaravelTranslatableBootForms\Form;

use TypiCMS\Form\Binding\BoundData;
use TypiCMS\Form\FormBuilder as BaseFormBuilder;

class FormBuilder extends BaseFormBuilder
{
    /**
     * Array of locale keys.
     */
    protected array $locales = [];

    /**
     * Sets the available locales for translatable fields.
     */
    public function setLocales(array $locales): void
    {
        $this->locales = $locales;
    }

    public function bind(mixed $data): void
    {
        $this->boundData = new BoundData($data);
    }

    /**
     * Getting value from Model or ModelTranslation to populate form.
     */
    protected function getBoundValue(string $name, ?string $default): mixed
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
