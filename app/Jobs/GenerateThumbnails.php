<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class GenerateThumbnails implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The media instance.
     *
     * @var \App\Models\Media
     */
    protected $media;

    /**
     * The formats to generate.
     *
     * @var array
     */
    protected $formats;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->media->id;
    }

    /**
     * Create a new job instance.
     */
    public function __construct(Media $media, array $formats)
    {
        $this->media = $media->withoutRelations();
        $this->formats = $formats;
    }

    /**
     * Execute the job.
     */
    public function handle(): bool
    {
        if ($this->media->isImage()) {
            $path = $this->media->path;
            $extension = $this->media->getExtension();
            $originalName = $this->media->name;
            $originalSize = Storage::disk($this->media->disk)->size($this->media->uri);
        } else {
            return false;
        }

        // Remove previous versions of the media
        if ($this->media->thumbnails) {
            if (! empty($this->formats)) {
                $thumbnails = array_filter($this->media->thumbnails, function ($format) {
                    return in_array($format, $this->formats);
                }, ARRAY_FILTER_USE_KEY);
            } else {
                $thumbnails = $this->media->thumbnails;
            }

            foreach ($thumbnails as $format => $thumbnail) {
                if (isset($thumbnail['uri'])) {
                    Storage::disk($this->media->disk)->delete($thumbnail['uri']);
                }

                if (isset($thumbnail['webp_uri'])) {
                    Storage::disk($this->media->disk)->delete($thumbnail['webp_uri']);
                }
            }
        }

        // Store the original version
        $image = Image::make($path);

        // Orientate the image using EXIF data
        $image->orientate();
        $image->save($path);

        // Save the original image dimensions
        $this->media->width = $image->width();
        $this->media->height = $image->height();

        $thumbnails = $this->media->thumbnails ?? [
            'original' => [
                'uri' => $originalName,
                'width' => $image->width(),
                'height' => $image->height(),
                'size' => $originalSize,
            ],
        ];

        $image->destroy();

        if (empty($this->formats)) {
            $formats = array_keys(config('settings.image_sizes'));
        } else {
            $formats = $this->formats;
        }

        foreach ($formats as $format) {
            $options = config("settings.image_sizes.{$format}");

            $originalFilename = str_replace(".{$extension}", "_{$format}.{$extension}", $originalName);
            $webpFilename = str_replace(".{$extension}", "_{$format}.webp", $originalName);
            $originalUri = "{$this->media->getPath()}{$originalFilename}";
            $webpUri = "{$this->media->getPath()}{$webpFilename}";

            $image = Image::make($path);

            $width = $image->width();
            $height = $image->height();

            if ($options['crop']) {
                $image->fit($options['width'], $options['height']);
            } else {
                // Skip if the image is smaller than the target size
                if ($width <= $options['width'] && $height <= $options['height']) {
                    continue;
                }

                $image->resize($options['width'], $options['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $image->interlace();

            $webpImage = clone $image;
            $webpImage->encode('webp');

            $image->encode($extension);

            // Save thumbnails in storage
            $image->save(Storage::disk($this->media->disk)->path($originalUri), 70);
            $webpImage->save(Storage::disk($this->media->disk)->path($webpUri), 70);

            // Update json
            $thumbnails[$format] = [
                'uri' => $originalUri,
                'size' => Storage::size($originalUri),
                'width' => $image->width(),
                'height' => $image->height(),
                'webp_uri' => $webpUri,
                'webp_size' => Storage::size($webpUri),
            ];

            // Destroy images to free up memory
            $image->destroy();
            $webpImage->destroy();
        }

        $this->media->thumbnails = $thumbnails;
        $this->media->save();

        return true;
    }
}
