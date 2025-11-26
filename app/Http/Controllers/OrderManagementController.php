<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    //
    public function adminOrderManagementIndex()
    {
        return view('orderManagement.adminOrderManagement');
    }

    public function createOrders()
    {
        return view('orderManagement.createOrders');
    }

    public function createPurchaseOrders()
    {
        return view('orderManagement.createPurchaseOrder');
    }
}
