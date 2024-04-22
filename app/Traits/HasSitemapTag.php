<?php

namespace App\Traits;

use Spatie\Sitemap\Tags\Url;

trait HasSitemapTag
{
    /**
     * Get the URL for the sitemap.
     */
    public function toSitemapTag(): Url|string|array
    {
        return $this->url;
    }
}
