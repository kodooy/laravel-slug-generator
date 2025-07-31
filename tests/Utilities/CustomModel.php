<?php

namespace Kodooy\SlugGenerator\Tests\Utilities;

use Kodooy\SlugGenerator\Traits\Slugable;
use Illuminate\Database\Eloquent\Model;

class CustomModel extends Model
{
    use Slugable;

    /**
     * This method controls if slug will change on model update. If you want
     * the slug to be generated only once on model creation then set this
     * option to true. Otherwise slug can change on every model update.
     * 
     * @return Boolean
     */
    protected function preserveSlugOnUpdate()
    {
        return true;
    }

    /**
     * This method sets which model attribute will be used to generate slug.
     * 
     * @return String
     */
    protected function slugableAttribute()
    {
        return 'title';
    }

    protected $table = 'dummy_model_table';
}
