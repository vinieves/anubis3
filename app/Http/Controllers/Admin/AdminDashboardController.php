<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    /**
     * Exibe o dashboard principal
     */
    public function index()
    {
        return view('admin.dashboard.index');
    }
}

