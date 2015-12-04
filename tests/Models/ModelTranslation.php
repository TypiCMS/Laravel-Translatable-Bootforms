<?php namespace Propaganistas\LaravelTranslatableBootForms\Tests\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ModelTranslation extends Eloquent
{

    protected $table = 'model_translations';

    public $timestamps = false;

    protected $fillable = ['id', 'input'];

}