<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard');
    }
}
