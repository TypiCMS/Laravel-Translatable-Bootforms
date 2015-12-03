<?php namespace Propaganistas\LaravelTranslatableBootForms\BootForms;

use AdamWathan\BootForms\HorizontalFormBuilder as _HorizontalFormBuilder;
use Propaganistas\LaravelTranslatableBootForms\BootForms\Elements\GroupWrapper;

class HorizontalFormBuilder extends _HorizontalFormBuilder
{
    protected function wrap($group)
    {
        return new GroupWrapper($group);
    }
}