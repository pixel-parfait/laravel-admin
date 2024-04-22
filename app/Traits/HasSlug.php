<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the HasSlug trait for a model.
     */
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if ($model->slug === null) {
                $count = 0;

                do {
                    $slug = Str::slug($model->{$model->sluggableAttribute});

                    if ($count > 0) {
                        $slug .= '-'.$count + 1;
                    }

                    $count++;
                } while (! $model->slugIsUnique($slug));

                $model->setAttribute('slug', $slug);
            }
        });
    }

    /**
     * Check if the specified slug is unique.
     */
    private function slugIsUnique(string $slug): bool
    {
        $slugUniqueForAttributes = $this->slugUniqueFor ?? [];

        return ! self::where('slug', $slug)
            ->where(function (Builder $query) use ($slugUniqueForAttributes) {
                foreach ($slugUniqueForAttributes as $attribute) {
                    $query->where($attribute, $this->{$attribute});
                }
            })
            ->select('id')
            ->exists();
    }
}
