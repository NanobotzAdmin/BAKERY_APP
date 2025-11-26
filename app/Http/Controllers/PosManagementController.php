<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosManagementController extends Controller
{
    //
    public function posView()
    {
        return view('pos.posView');
    }
}
