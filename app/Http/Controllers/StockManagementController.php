<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockManagementController extends Controller
{
    //
    public function adminStockManagementIndex()
    {
        return view('stockManagement.managestock');
    }

    public function admingrn()
    {
        return view('stockManagement.admingrn');
    }
}
