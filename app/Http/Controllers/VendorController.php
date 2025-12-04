<?php

namespace App\Http\Controllers;

use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return view('vendorManagement.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_name' => 'required',
            'vendor_email' => 'required|email|unique:vendor,vendor_email',
            'vendor_phone' => 'required',
            'vendor_address_line_1' => 'required',
            'vendor_address_line_2' => 'required',
            'vendor_city' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $vendor = new Vendor();
        $vendor->vendor_name = $request->vendor_name;
        $vendor->vendor_email = $request->vendor_email;
        $vendor->vendor_phone = $request->vendor_phone;
        $vendor->vendor_address_line_1 = $request->vendor_address_line_1;
        $vendor->vendor_address_line_2 = $request->vendor_address_line_2;
        $vendor->vendor_city = $request->vendor_city;
        $vendor->created_by = Auth::user()->id;
        $vendor->updated_by = Auth::user()->id;
        $vendor->save();

        return back()->with('success', 'Vendor Created Successfully');
    }


}
