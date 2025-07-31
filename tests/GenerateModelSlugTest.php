<?php

namespace Kodooy\SlugGenerator\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Kodooy\SlugGenerator\Tests\Utilities\CustomModel;
use Kodooy\SlugGenerator\Tests\Utilities\DefaultModel;
use PHPUnit\Framework\Attributes\Test;

class GenerateModelSlugTest extends TestCase
{
    use DatabaseMigrations;

    protected $model;

    public function setUp():void
    {
        parent::setUp();

        $this->model = new DefaultModel();

        Schema::create($this->model->getTable(), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
            $table->temporary();
        });
    }

    public function tearDown():void
    {
        Schema::dropIfExists($this->model->getTable());
        parent::tearDown();
    }

    #[Test]
    public function a_model_can_generate_unique_slug_from_name_attribute()
    {
        $modelOne = new DefaultModel();
        $modelOne->name = 'New title';
        $modelOne->save();

        $modelTwo = new DefaultModel();
        $modelTwo->name = 'New title';
        $modelTwo->save();

        $this->assertEquals('new-title', $modelOne->slug);
        $this->assertEquals('new-title-1', $modelTwo->slug);
    }

    #[Test]
    public function a_model_can_generate_unique_slug_from_custom_attribute()
    {
        $model = new CustomModel();
        $model->title = 'New title';
        $model->save();

        $this->assertEquals('new-title', $model->slug);
    }

    #[Test]
    public function a_model_can_preserve_slug_on_update_when_name_has_not_changed()
    {
        $this->model->name = 'New title';
        $this->model->save();

        $this->assertEquals('new-title', $this->model->slug);

        $this->model->name = 'New title';
        $this->model->save();

        $this->assertEquals('new-title', $this->model->slug);
    }

    #[Test]
    public function a_model_will_generate_new_slug_on_update_when_name_has_changed()
    {
        $this->model->name = 'New title';
        $this->model->save();

        $this->assertEquals('new-title', $this->model->slug);

        $this->model->name = 'Updated title';
        $this->model->save();

        $this->assertEquals('updated-title', $this->model->slug);
    }

    #[Test]
    public function a_model_can_preserve_slug_when_preserve_slug_on_update_method_returns_true()
    {
        $model = new CustomModel();
        $model->title = 'New title';
        $model->save();

        $this->assertEquals('new-title', $model->slug);

        $model->title = 'Changed title';
        $model->save();

        $this->assertEquals('new-title', $model->slug);
    }

    #[Test]
    public function a_model_will_generate_slug_with_lowest_possible_suffix()
    {
        $modelOne = new DefaultModel();
        $modelOne->name = 'New title';
        $modelOne->save();

        $modelTwo = new DefaultModel();
        $modelTwo->name = 'New title';
        $modelTwo->save();

        $this->assertEquals('new-title', $modelOne->slug);
        $this->assertEquals('new-title-1', $modelTwo->slug);

        $modelOne->name = 'Updated title';
        $modelOne->save();

        $modelTwo->name = 'New title';
        $modelTwo->save();

        $this->assertEquals('updated-title', $modelOne->slug);
        $this->assertEquals('new-title', $modelTwo->slug);
    }
}
