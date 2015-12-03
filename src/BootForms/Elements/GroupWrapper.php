<?php namespace Zephyr\Cms\Services\Form\Elements;

use AdamWathan\BootForms\Elements\GroupWrapper as _GroupWrapper;

class GroupWrapper extends _GroupWrapper
{
    public function addGroupClass($class)
    {
        $this->formGroup->addClass($class);

        return $this;
    }
}
