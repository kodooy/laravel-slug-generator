<?php

namespace Kodooy\SlugGenerator\Tests\Utilities;

use Kodooy\SlugGenerator\Traits\Slugable;
use Illuminate\Database\Eloquent\Model;

class DefaultModel extends Model
{
    use Slugable;

    protected $table = 'dummy_model_table';
}
