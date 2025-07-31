# Slug Generator Laravel Package

A Laravel package that provides automatic slug generation for Eloquent models using traits.

## Installation

```bash
composer require kodooy/laravel-slug-generator
```

## Database Setup

Your Eloquent model's database table must have a `slug` field. Add it to your migration:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique()->index();
    $table->timestamps();
});
```

If adding to an existing table:

```php
Schema::table('posts', function (Blueprint $table) {
    $table->string('slug')->unique()->index();
});
```

## Usage

Add the `Slugable` trait to your Eloquent model:

```php
use Kodooy\SlugGenerator\Traits\Slugable;

class Post extends Model
{
    use Slugable;

    // The trait will automatically generate slugs from the 'name' attribute
    // and use 'slug' as the route key
}
```

### Customization

Override these methods to customize behavior:

```php
class Post extends Model
{
    use Slugable;

    // Use different attribute for slug generation
    protected function slugableAttribute()
    {
        return 'title'; // default is 'name'
    }

    // Preserve existing slugs on update
    protected function preserveSlugOnUpdate()
    {
        return true; // default is false
    }
}
```

## Requirements

- PHP ^8.1
- Laravel ^9.0|^10.0|^11.0|^12.0

## License

MIT