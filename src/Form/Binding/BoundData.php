<?php namespace Propaganistas\LaravelTranslatableBootForms\Form\Binding;

use AdamWathan\Form\Binding\BoundData as BaseBoundData;

class BoundData extends BaseBoundData
{
    public function data()
    {
        return $this->data;
    }
}
