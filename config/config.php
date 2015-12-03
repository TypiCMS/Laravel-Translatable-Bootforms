<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Form Group class
	|--------------------------------------------------------------------------
	|
	| The class the form group of a localized form element will receive additionally.
	| Useful for showing/hiding only form inputs of a certain language.
	|
	| E.g. for 'form-group-translation':
	|   <div class="form-group form-group-translation">
	|     <input ... />
	|   </div>
	|
	*/

	'form-group-class' => 'form-group-translation',

	/*
	|--------------------------------------------------------------------------
	| Label override
	|--------------------------------------------------------------------------
	|
	| The label to use when creating a localized form element.
	| Useful for indicating that the form element is meant for a certain language.
	| To have the label itself translated you still need to pass a trans() value as label in the view.
	| 
	| Available placeholders:
	|   %label   The original (eventually translated) label.
	|   %locale  The locale
	| 
	| E.g. for '%label (%locale)' and TranslatableBootForm::text(trans('nameTranslationKey'), 'name')
	| this would yield the following labels:
	|   <label for="en[name]">Name (en)</label>
	|   <label for="nl[name]">Naam (nl)</label>
	|
	*/

	'label-locale-indicator' => '<span>%label</span><span class="label label-default">%locale</span>',

	/*
	|--------------------------------------------------------------------------
	| Input locale attribute
	|--------------------------------------------------------------------------
	|
	| The attribute to use when attaching the corresponding locale to a localized form element.
	| Useful for showing/hiding only form inputs of a certain language.
	| 
	| E.g. for 'data-language':
	|   <input name="en[someName]" data-language="en" />
	|
	*/

	'input-locale-attribute' => 'data-language',

];
