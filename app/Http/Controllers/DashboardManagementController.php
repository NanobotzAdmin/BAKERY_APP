<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardManagementController extends Controller
{
    //
    public function adminDashboard()
    {
        return view('dashboard.adminDashboard');
    }
}
