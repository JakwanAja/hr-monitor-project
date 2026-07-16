<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('assistant.dashboard');
    }
}