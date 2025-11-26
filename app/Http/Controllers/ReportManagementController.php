<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportManagementController extends Controller
{
    //
    public function adminSalesReportIndex()
    {
        return view('reports.adminSalesReport');
    }

    public function adminStockReportIndex()
    {
        return view('reports.adminStockReport');
    }
}
