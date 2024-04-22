<?php

namespace App\Models;

use App\Jobs\GenerateThumbnails;
use App\Jobs\RemoveThumbnails;
use App\Support\Converter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'uri',
        'disk',
        'size',
        'width',
        'height',
        'mime_type',
        'thumbnails',
        'alt',
        'caption',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'url',
        'srcset',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'thumbnails' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function ($media) {
            // Remove files when deleting the media from database
            if (is_array($media->thumbnails)) {
                foreach ($media->thumbnails as $thumbnail) {
                    Storage::delete($thumbnail['uri']);
                }
            }

            Storage::delete($media->uri);
        });
    }

    /**
     * Get the file's URL.
     */
    public function getUrlAttribute(): ?string
    {
        if (Storage::exists($this->uri)) {
            return Storage::url($this->uri);
        }

        return null;
    }

    /**
     * Get the file's path.
     */
    public function getPathAttribute(): string
    {
        return Storage::path($this->uri);
    }

    /**
     * Get the file's readable size.
     */
    public function getSizeAttribute(int $size): string
    {
        return Converter::readableFilesize($size);
    }

    /**
     * Get the file's type.
     */
    public function getTypeAttribute(): string
    {
        if (Str::startsWith($this->mime_type, 'image')) {
            return 'image';
        } elseif (Str::startsWith($this->mime_type, 'video')) {
            return 'video';
        } elseif (Str::startsWith($this->mime_type, 'audio')) {
            return 'audio';
        } elseif ($this->mime_type === 'application/pdf') {
            return 'pdf';
        }

        return 'other';
    }

    /**
     * Get the srcset for the media.
     */
    public function getSrcSetAttribute(): ?string
    {
        $srcset = [];

        if ($this->thumbnails) {
            foreach ($this->thumbnails as $format => $data) {
                // Exclude cropped images
                if (config("settings.image_sizes.{$format}.crop")) {
                    continue;
                }

                if (! Storage::exists($data['webp_uri'] ?? $data['uri'])) {
                    continue;
                } else {
                    $url = Storage::url($data['webp_uri'] ?? $data['uri']);
                }

                $srcset[] = "{$url} {$data['width']}w";
            }
        }

        if (empty($srcset)) {
            return null;
        }

        return implode(', ', $srcset);
    }

    /**
     * Get the file's extension.
     */
    public function getExtension(): string
    {
        return File::extension($this->name);
    }

    /**
     * Get the file's path.
     */
    public function getPath(): string
    {
        return str_replace($this->name, '', $this->uri);
    }

    /**
     * Check if the media is an image.
     */
    public function isImage(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Check if the media is a video.
     */
    public function isVideo(): bool
    {
        return preg_match('/^video/', $this->mime_type);
    }

    /**
     * Get the URL for the specified format.
     */
    public function getFormat(string $format, bool $asArray = true, bool $webp = true): mixed
    {
        if (! $this->isImage()) {
            return null;
        }

        if ($this->thumbnails) {
            if (isset($this->thumbnails[$format])) {
                $uri = $webp && $this->thumbnails[$format]['webp_uri'] ? $this->thumbnails[$format]['webp_uri'] : $this->thumbnails[$format]['uri'];
                $width = $this->thumbnails[$format]['width'];
                $height = $this->thumbnails[$format]['height'];
            } else {
                $uri = $this->uri;
            }
        } else {
            $uri = $this->uri;
        }

        if (! Storage::exists($uri)) {
            return null;
        } else {
            $url = Storage::url($uri);
        }

        if ($asArray) {
            return [
                'width' => $width ?? null,
                'height' => $height ?? null,
                'url' => $url,
            ];
        }

        return $url;
    }

    /**
     * Get the img tag for the media.
     */
    public function getImgTag(...$classes): ?string
    {
        $thumbnail = $this->getFormat('thumbnail');

        if (! $thumbnail) {
            return null;
        }

        $attributesArray = [
            'loading' => 'lazy',
            'src' => $thumbnail['url'],
            'alt' => $this->alt,
        ];

        if (is_array($classes)) {
            $attributesArray['class'] = implode(' ', $classes);
        }

        if ($srcset = $this->srcset) {
            $attributesArray['srcset'] = $srcset;
        }

        $attributesArray['width'] = $thumbnail['width'];
        $attributesArray['height'] = $thumbnail['height'];

        $attributes = implode(' ', array_map(fn ($key) => "{$key}=\"{$attributesArray[$key]}\"", array_keys($attributesArray)));

        return "<img {$attributes} />";
    }

    /**
     * Get the required data for the Image Vue component.
     */
    public function getImgData(): mixed
    {
        return $this->only(config('settings.image_attributes'));
    }

    /**
     * Generate thumbnails.
     */
    public function generateThumbnails(array $formats = [], $sync = false): void
    {
        if ($sync) {
            GenerateThumbnails::dispatchSync($this, $formats);
        } else {
            GenerateThumbnails::dispatch($this, $formats);
        }
    }

    /**
     * Remove thumbnails.
     */
    public function removeThumbnails(): void
    {
        RemoveThumbnails::dispatch($this);
    }

    /**
     * Upload and create a new Media.
     */
    public static function upload(UploadedFile $file, ?string $path = null, ?string $disk = null, $sync = false): self
    {
        $pathinfo = pathinfo($file->getClientOriginalName());
        $filename = Str::slug($pathinfo['filename']);
        $extension = $pathinfo['extension'];

        if (! $disk) {
            $disk = config('filesystems.default');
        }

        // Organize folders by date (if path is not specified)
        $path = $path ?: date('Y').'/'.date('m').'/'.date('d');

        // Append number to filename if the path already exists
        $count = self::where('uri', 'like', "{$path}/{$filename}%")->count() + 1;

        if ($count > 1) {
            $filename .= "-{$count}";
        }

        $name = "{$filename}.{$extension}";

        $uri = $file->storeAs($path, $name, $disk);
        $mime = $file->getMimeType();

        // Create model
        $media = self::create([
            'name' => $name,
            'disk' => $disk,
            'uri' => $uri,
            'size' => $file->getSize(),
            'mime_type' => $mime,
        ]);

        if (preg_match('/^image/', $mime)) {
            $media->generateThumbnails([], $sync);
        }

        return $media;
    }

    /**
     * Replace a file by another.
     */
    public function replace(UploadedFile $file, ?string $path = null): self
    {
        $pathinfo = pathinfo($file->getClientOriginalName());
        $filename = Str::slug($pathinfo['filename']);
        $extension = $pathinfo['extension'];

        // Organize folders by date (if path is not specified)
        $path = $path ?: $this->getPath();

        // Append number to filename if the path already exists
        $count = self::where('id', '!=', $this->id)->where('uri', 'like', "{$path}/{$filename}%")->count() + 1;

        if ($count > 1) {
            $filename .= "-{$count}";
        }

        $name = "{$filename}.{$extension}";

        $uri = $file->storeAs($path, $name, $this->disk);
        $mime = $file->getMimeType();

        // Update media
        $this->update([
            'name' => $name,
            'uri' => $uri,
            'size' => $file->getSize(),
            'mime_type' => $mime,
        ]);

        if ($this->isImage()) {
            $this->generateThumbnails();
        }

        return $this;
    }
}
