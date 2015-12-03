<?php namespace Propaganistas\LaravelTranslatableBootForms\BootForms;

use AdamWathan\BootForms\BasicFormBuilder as _BasicFormBuilder;
use Propaganistas\LaravelTranslatableBootForms\BootForms\Elements\GroupWrapper;

class BasicFormBuilder extends _BasicFormBuilder
{
    protected function wrap($group)
    {
        return new GroupWrapper($group);
    }
}