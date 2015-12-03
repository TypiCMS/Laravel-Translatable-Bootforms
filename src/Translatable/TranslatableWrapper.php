<?php namespace Propaganistas\LaravelTranslatableBootForms\Translatable;

use Dimsav\Translatable\Translatable;

class TranslatableWrapper {

	use Translatable {
		Translatable::getLocales as _getLocales;
	}

	/**
	 * @return array
	 *
	 * @throws \Dimsav\Translatable\Exception\LocalesNotDefinedException
	 */
	public function getLocales()
	{
		return $this->_getLocales();
	}

}
