<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    /**
     * Display a listing of the media.
     */
    public function index(Request $request): mixed
    {
        $mediaQuery = Media::orderBy('id', 'desc');

        if ($request->search) {
            $mediaQuery->where('name', 'like', "%{$request->search}%")
                ->orWhere('caption', 'like', "%{$request->search}%");
        }

        if ($request->type) {
            if ($request->type === 'other') {
                $mediaQuery->where('mime_type', 'not like', 'image%')
                    ->where('mime_type', 'not like', 'video%')
                    ->where('mime_type', 'not like', 'audio%')
                    ->where('mime_type', '!=', 'application/pdf')
                    ->where('mime_type', '!=', 'image/svg+xml');
            } else {
                $mediaQuery->where('mime_type', 'like', "{$request->type}%");
            }
        }

        $medias = $mediaQuery->paginate(36)
            ->onEachSide(1)
            ->withQueryString()
            ->through(fn ($media) => [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->url,
                'thumbnail' => $media->getImgTag('mceEditable'),
                'size' => $media->size,
                'mime_type' => $media->mime_type,
                'alt' => $media->alt,
                'caption' => $media->caption,
            ]);

        if ($request->expectsJson()) {
            return response()->json($medias);
        }

        return Inertia::render('MediaLibrary/Index', [
            'filters' => $request->all('search', 'type'),
            'medias' => $medias,
        ]);
    }

    /**
     * Show the form for editing the specified media.
     */
    public function edit(Media $media): JsonResponse
    {
        return response()->json([
            'id' => $media->id,
            'name' => $media->name,
            'url' => $media->url,
            'thumbnail' => $media->getImgTag(),
            'type' => $media->type,
            'alt' => $media->alt,
            'caption' => $media->caption,
        ]);
    }

    /**
     * Update the specified media in storage.
     */
    public function update(Request $request, Media $media): RedirectResponse
    {
        $media->update([
            'alt' => $request->alt,
            'caption' => $request->caption,
        ]);

        return redirect()->back()->with('success', 'Le fichier a été mis à jour.');
    }

    /**
     * Retrieve a media by its ID.
     */
    public function load(Media $media): BinaryFileResponse
    {
        $path = Storage::disk($media->disk)->path($media->uri);

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="'.$media->name.'"',
        ]);
    }

    /**
     * Store a newly created media in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $media = Media::upload($request->file, $request->path, $request->disk, $request->sync);

        return response()->json($media->id);
    }

    /**
     * Upload multiple files.
     */
    public function uploadMultiple(Request $request): JsonResponse
    {
        $mediaIds = [];

        if ($request->file('files')) {
            foreach ($request->file('files') as $file) {
                if ($media = Media::upload($file, $request->path, $request->disk, $request->sync)) {
                    $mediaIds[] = $media->id;
                }
            }
        }

        return response()->json([
            'ids' => $mediaIds,
        ]);
    }

    /**
     * Remove the specified media from storage.
     */
    public function destroy(Media $media): RedirectResponse
    {
        $media->delete();

        return redirect()->back()->with('success', 'Le fichier a été supprimé.');
    }
}
