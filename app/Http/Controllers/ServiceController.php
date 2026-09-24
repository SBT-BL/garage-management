<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display the services placeholder page.
     */
    public function index(): View
    {
        return view('services.index');
    }
}
