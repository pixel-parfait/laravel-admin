<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Draftable
{
    /**
     * Scope a query to only include published events.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', 1);
    }
}
