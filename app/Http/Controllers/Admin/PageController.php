<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    use AuthorizesRequests;

    /**
     * @var array
     */
    private $formOptions = [];

    public function __construct()
    {
        $this->authorizeResource(Page::class, 'page');

        $this->formOptions = [
            'sections' => config('settings.sections'),
        ];
    }

    /**
     * Display a listing of the pages.
     */
    public function index(Request $request): Response
    {
        $pagesQuery = Page::orderBy('section')->orderBy('order');

        if ($request->search) {
            $pagesQuery->where('title', 'like', "%{$request->search}%");
        }

        if (isset($request->published)) {
            $pagesQuery->where('published', $request->published);
        }

        if ($request->status === 'trashed') {
            $pagesQuery->onlyTrashed();
        }

        $pages = $pagesQuery
            ->paginate(25)
            ->onEachSide(2)
            ->withQueryString()
            ->through(fn ($page) => [
                'id' => $page->id,
                'title' => $page->title,
                'path' => $page->path,
                'published' => $page->published,
                'deleted_at' => $page->deleted_at,
            ]);

        return Inertia::render('Pages/Index', [
            'filters' => $request->all('search', 'published', 'status'),
            'pages' => $pages,
            'total_published' => Page::count(),
            'total_trashed' => Page::onlyTrashed()->count(),
        ]);
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(): Response
    {
        return Inertia::render('Pages/Create', [
            'options' => $this->formOptions,
        ]);
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(StorePageRequest $request): RedirectResponse
    {
        Page::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'template' => $request->template,
            'section' => $request->section,
            'cover_id' => $request->cover_id,
            'published' => false,
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'La page a été créée.');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page): Response
    {
        return Inertia::render('Pages/Edit', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'section' => $page->section,
                'content' => $page->content ?: new \StdClass,
                'cover_id' => $page->cover_id,
                'meta_description' => $page->meta_description,
                'order' => $page->order,
                'published' => $page->published,
                'locked' => $page->locked,
                'url' => $page->url,
                'deleted_at' => $page->deleted_at,
            ],
            'template' => $page->template ?: 'default',
            'options' => $this->formOptions,
        ]);
    }

    /**
     * Update the specified page in storage.
     */
    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $data = [
            'title' => $request->title,
            'section' => $request->section,
            'content' => $request->content,
            'cover_id' => $request->cover_id,
            'meta_description' => $request->meta_description,
            'order' => $request->order,
            'published' => $request->published,
        ];

        if ($page->slug) {
            $data['slug'] = $request->slug;
        }

        $page->update($data);

        return redirect()->back()->with('success', 'La page a été mise à jour.');
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page): RedirectResponse
    {
        if ($page->deleted_at) {
            $page->forceDelete();

            return redirect()
                ->route('admin.pages.index', ['status' => 'trashed'])
                ->with('success', 'La page a été supprimée définitivement.');
        }

        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'La page a été placée à la corbeille.');
    }

    /**
     * Restore the specified page from trash.
     */
    public function restore(Page $page): RedirectResponse
    {
        $page->restore();

        return redirect()
            ->route('admin.pages.edit', compact('page'))
            ->with('success', 'La page a été restaurée.');
    }
}
