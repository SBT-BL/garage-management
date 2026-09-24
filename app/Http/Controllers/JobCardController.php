<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class JobCardController extends Controller
{
    /**
     * Display the job cards placeholder page.
     */
    public function index(): View
    {
        return view('job-cards.index');
    }
}
