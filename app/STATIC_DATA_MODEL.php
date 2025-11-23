<?php

/**
 * Created by RAZOR
 * */

namespace App;

class STATIC_DATA_MODEL
{
    // Company Details
    public static $company_name = "BAKERYMATE";
    public static $company_title = "";
    public static $company_address = "Colombo, Colombo";
    public static $company_email = "";
    public static $company_contacts = "071****123 / 071****123";
    public static $company_logo = "img/bakerymate.png";
    public static $company_logo_without_bg = "img/logo.png";

    // Activity Types
    public static $insert = 1;
    public static $update = 2;
    public static $delete = 3;
    public static $search = 4;
    public static $click = 5;
    public static $logIn = 6;
    public static $logOut = 7;

    // Status
    public static $Active = 1;
    public static $Inactive = 0;

    public static $imageShow = 1;
    public static $imageHide = 0;

    public static $admin = 1;
    public static $user = 2;

    public static $add = 1;
    public static $remove = 0;

    public static $visible = 1;
    public static $nonVisible = 0;

    // Invoice Types
    public static $credit = 1;
    public static $cash = 2;
    public static $cheque = 3;

    // Invoice Status
    public static $invoicePending = 0;
    public static $invoiceCompleted = 1;
    public static $invoiceDeleted = 3;

    // Payment Status
    public static $Payment_Pending = 0;
    public static $Payment_Active = 1;
    public static $Payment_Rejected = 3;

    // Delivery Vehicle Status
    public static $deliveryPending = 0;
    public static $deliveryLoaded = 1;
    public static $deliveryCompleted = 2;
    public static $deliveryDeleted = 3;

    // User Roles
    public static $userrole_nanobotzAdmin = 1;
    public static $userrole_systemAdmin = 2;
    public static $userrole_salesRep = 3;
    public static $userrole_driver = 4;
    public static $userrole_manager = 5;

    // Product Item States (pm_product_item_state)
    public static $packed = 1;
    public static $unpacked = 2;


    // Order Take Status
    public static $orderTakePending = 0;
    public static $orderTakeCompleted = 1;
    public static $orderTakeDeleted = 3;

    // Variation Value Types
    public static $variationValueTypeL = 1;
    public static $variationValueTypeML = 2;
    public static $variationValueTypeG = 3;
    public static $variationValueTypeKG = 4;

    public static $productItemTypes = [
        [
            'id' => 1,
            'name' => 'Selling Product'
        ],
        [
            'id' => 2,
            'name' => 'Row Material'
        ]
    ];
}
