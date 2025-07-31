<?php

namespace Kodooy\SlugGenerator\Traits;

use Illuminate\Support\Str;

trait Slugable
{
    /**
     * Initial slug for the model.
     *
     * @var String
     */
    protected $initialSlug;

    /**
     * Similar slugs.
     *
     * @var Collection
     */
    protected $similarSlugs;

    /**
     * The "booting" method of the model.
     *
     * @return Void
     */
    public static function bootSlugable()
    {
        static::saving(function ($model) {
            if ($model->slug && $model->preserveSlugOnUpdate()) {
                return;
            }

            $model->slug = $model->generateSlug();
        });
    }

    /**
     * Get the route key for the model binding.
     *
     * @return String
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Generate unique slug for model.
     *
     * @return String
     */
    public function generateSlug()
    {
        $this->setInitialSlug();

        if ($this->canUseInitialSlug()) {
            return $this->initialSlug;
        }

        if ($this->canUseCurrentSlug()) {
            return $this->slug;
        }

        return $this->generateUniqueSlug();
    }

    /**
     * Set initial slug and get similar slugs for the model.
     *
     * @return Void
     */
    protected function setInitialSlug()
    {
        $this->initialSlug = Str::slug($this->{$this->slugableAttribute()});
        $this->similarSlugs = $this->getSimilarSlugs($this->initialSlug);
    }

    /**
     * Get similar slugs for given string.
     *
     * @param  String      $slug
     * @return Collection
     */
    protected function getSimilarSlugs($slug)
    {
        $query = self::select('slug')
            ->where('slug', $slug)
            ->orWhere('slug', 'LIKE', $slug . '-%')
            ->orderBy('slug', 'desc');

        // Exclude current model if it has an ID (existing model)
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return $query->get()->pluck('slug');
    }

    /**
     * Check if initial slug can be used as model slug.
     *
     * @return Boolean
     */
    protected function canUseInitialSlug()
    {
        return !$this->similarSlugs->contains($this->initialSlug);
    }

    /**
     * Check if model can use current slug.
     *
     * @return Boolean
     */
    protected function canUseCurrentSlug()
    {
        if (!$this->slug) {
            return false;
        }

        return $this->similarSlugs->contains($this->slug);
    }

    /**
     * Generate unique slug with proper suffix.
     *
     * @return String
     */
    protected function generateUniqueSlug()
    {
        $suffix = 1;
        $slug = '';

        do {
            $slug = $this->initialSlug . '-' . $suffix;

            if (!$this->similarSlugs->contains($slug)) {
                break;
            }

            $suffix++;
        } while (1);

        return $slug;
    }

    /**
     * Check if slug should change on model update.
     *
     * @return Boolean
     */
    protected function preserveSlugOnUpdate()
    {
        return false;
    }

    /**
     * Set which model attribute will be used to generate slug.
     *
     * @return String
     */
    protected function slugableAttribute()
    {
        return 'name';
    }
}
