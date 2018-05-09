<?php

namespace TypiCMS\LaravelTranslatableBootForms\Tests\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Spatie\Translatable\HasTranslations;

class Model extends Eloquent
{
    use HasTranslations;

    protected $table = 'models';

    public $timestamps = false;

    protected $fillable = ['id', 'default', 'input'];

    public $translatable = ['input'];
}
