<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the dashboard page.
     */
    public function index(): Response
    {
        return Inertia::render('Dashboard');
    }

    /**
     * Clear the application cache.
     */
    public function clearCache(): RedirectResponse
    {
        Artisan::call('cache:clear');

        return redirect()->back()->with('success', 'Le cache a été vidé.');
    }
}
