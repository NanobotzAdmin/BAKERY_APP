<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalarySlipController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/admin_login', function () {
    return view('login');
});

// Route::get('/adminStockInReport', function () {
//     return view('reports.StockInReport.stockInReport');
// });

// Route::get('/admindashboard', function () {
//     return view('welcome');
// });

// Route::get('/adminStockReport', function () {
//    return view('reports.stockReport');
// });

// Route::get('/adminSalesReport', function () {
//    return view('reports.salesReport');
// });

// Route::get('/adminRouteWiseSalesReport', function () {
//    return view('reports.SalesReport.adminRouteWiseSalesReport');
//  });

// Route::get('/adminManageProducts', function () {
//     return view('');
//  });

Route::get('/', function () {
    return view('login');
});

Route::get('/salesrepDashboard', function () {
    return view('salesrepDashboard');
});

Route::get('/routeWiseCreditReportPrint', function () {
    return view('reports.routeWiseCreditReportPrint');
});

Route::get('/generateSalarySlipPrint', function () {
    return view('reports.generateSalarySlipPrint');
});

Route::get('/adminProfitAndLossStatement', function () {
    return view('reports.adminProfitAndLossStatement');
});


Route::get('/admindashboard', 'HomeManagementController@admindashboardIndex')->middleware('userAuth:admindashboard');
Route::post('/loadDeliveryModalDashboard', 'HomeManagementController@loadDeliveryModalDashboard')->middleware('loggedchecked:loadDeliveryModalDashboard');

///////////////////////////////////////////////// USER CONTROLLER BEGIN HERE ////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminUserManagement', 'UserManagementController@adminUserManagementIndex')->middleware('userAuth:adminUserManagement');
Route::post('/saveUser', 'UserManagementController@saveUser')->middleware('loggedchecked:loadCategoryDataToModal');
Route::post('/viewUserDataToModal', 'UserManagementController@viewUserDataToModal')->middleware('loggedchecked:viewUserDataToModal');
Route::post('/updateUserdata', 'UserManagementController@updateUserdata')->middleware('loggedchecked:updateUserdata');
Route::get('/deleteUser/{id}', 'UserManagementController@deleteUser')->middleware('loggedchecked:deleteUser');
///////////////////////////////////////////////// USER CONTROLLER END HERE //////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// LOGIN CONTROLLER BEGIN HERE ///////////////////////////////////////////////////////////////////////////////////////////////////////
Route::post('/adminLogin', 'LoginController@adminLogin');
Route::get('/logout', 'LoginController@logout');
///////////////////////////////////////////////// LOGIN CONTROLLER END HERE /////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// INTERFACE CONTROLLER BEGIN HERE ///////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminInterfaceManagement', 'InterfaceManagementController@adminInterfaceManagementIndex')->middleware('userAuth:adminInterfaceManagement');
Route::post('/saveInterfaceTopic', 'InterfaceManagementController@saveInterfaceTopic')->middleware('loggedchecked:saveInterfaceTopic');
Route::post('/saveInterface', 'InterfaceManagementController@saveInterface')->middleware('loggedchecked:saveInterface');
Route::post('/saveInterfaceComponent', 'InterfaceManagementController@saveInterfaceComponent')->middleware('loggedchecked:saveInterfaceComponent');
Route::post('/loadInterfaces', 'InterfaceManagementController@loadInterfaces')->middleware('loggedchecked:loadInterfaces');
Route::post('/loadInterfaceDataToModal', 'InterfaceManagementController@loadInterfaceDataToModal')->middleware('loggedchecked:loadInterfaceDataToModal');
// loading data to interface modals
Route::post('/loadInterfaceTopicDetailsToModal', 'InterfaceManagementController@loadInterfaceTopicDetailsToModal')->middleware('loggedchecked:loadInterfaceTopicDetailsToModal');
Route::post('/loadInterfaceDetailsToModal', 'InterfaceManagementController@loadInterfaceDetailsToModal')->middleware('loggedchecked:loadInterfaceDetailsToModal');
Route::post('/loadInterfaceComponentDetailsToModal', 'InterfaceManagementController@loadInterfaceComponentDetailsToModal')->middleware('loggedchecked:loadInterfaceComponentDetailsToModal');
// update --> interface Topic, Interface, interface Component
Route::post('/updateInterfaceTopic', 'InterfaceManagementController@updateInterfaceTopic')->middleware('loggedchecked:updateInterfaceTopic');
Route::post('/updateInterface', 'InterfaceManagementController@updateInterface')->middleware('loggedchecked:updateInterface');
Route::post('/updateInterfaceComponent', 'InterfaceManagementController@updateInterfaceComponent')->middleware('loggedchecked:updateInterfaceComponent');
///////////////////////////////////////////////// INTERFACE CONTROLLER END HERE ////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// CUSTOMER CONTROLLER BEGIN HERE ///////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminCustomerManagement', 'CustomerManagementController@adminCustomerManagementIndex')->middleware('userAuth:adminCustomerManagement');
Route::post('/loadAllCustomersDetails', 'CustomerManagementController@loadAllCustomersDetails')->middleware('loggedchecked:loadAllCustomersDetails');
Route::post('/saveCustomer', 'CustomerManagementController@saveCustomer')->middleware('loggedchecked:saveCustomer');
Route::post('/loadCusDataToModal', 'CustomerManagementController@loadCusDataToModal')->middleware('loggedchecked:loadCusDataToModal');
Route::post('/updateCustomer', 'CustomerManagementController@updateCustomer')->middleware('loggedchecked:updateCustomer');
Route::post('/viewCustomerRackModel', 'CustomerManagementController@viewCustomerRackModel')->middleware('loggedchecked:viewCustomerRackModel');
Route::post('/cutomerRackCountUpdate', 'CustomerManagementController@cutomerRackCountUpdate')->middleware('loggedchecked:cutomerRackCountUpdate');
Route::get('/customerJson', 'CustomerManagementController@customerJson')->middleware('loggedchecked:customerJson');
// Route::get('/customerdelete/{id}', ['middleware' => 'loggedchecked:customerdelete', 'uses' => 'CustomerManagementController@customerdelete', function () {}]);
Route::post('/customerdelete', 'CustomerManagementController@customerdelete')->middleware('loggedchecked:customerdelete');
Route::get('/adminCustomerRegistration', 'CustomerManagementController@adminCustomerRegistrationIndex')->middleware('userAuth:adminCustomerRegistration');
///////////////////////////////////////////////// CUSTOMER CONTROLLER END HERE ////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// VEHICLE CONTROLLER BEGIN HERE ///////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminVehicleManagement', 'VehicleManagementController@adminVehicleManagementIndex')->middleware('userAuth:adminVehicleManagement');
Route::post('/saveVehicle', 'VehicleManagementController@saveVehicle')->middleware('loggedchecked:saveVehicle');
Route::post('/loadVehicleDataToModal', 'VehicleManagementController@loadVehicleDataToModal')->middleware('loggedchecked:loadVehicleDataToModal');
Route::post('/updateVehicle', 'VehicleManagementController@updateVehicle')->middleware('loggedchecked:updateVehicle');
Route::post('/changeVehicleStatus', 'VehicleManagementController@changeVehicleStatus')->middleware('loggedchecked:changeVehicleStatus');

// Arrange a Delivery Vehicle
Route::get('/adminDeliveryVehicleManagement', 'VehicleManagementController@adminDeliveryVehicleManagementIndex')->middleware('userAuth:adminDeliveryVehicleManagement');
Route::post('/saveDeliveryVehicle', 'VehicleManagementController@saveDeliveryVehicle')->middleware('loggedchecked:saveDeliveryVehicle');
Route::post('/loadItemsModal', 'VehicleManagementController@loadItemsModal')->middleware('loggedchecked:loadItemsModal');
Route::post('/loadBatchDetails', 'VehicleManagementController@loadBatchDetails')->middleware('loggedchecked:loadBatchDetails');
Route::post('/saveDeliveryData', 'VehicleManagementController@saveDeliveryData')->middleware('loggedchecked:saveDeliveryData');
Route::post('/completeDelivery', 'VehicleManagementController@completeDelivery')->middleware('loggedchecked:completeDelivery');
Route::post('/viewDeliveryData', 'VehicleManagementController@viewDeliveryData')->middleware('loggedchecked:viewDeliveryData');
// Update Returns
Route::post('/loadUpdateModalOfUnloadingsAndReturns', 'VehicleManagementController@loadUpdateModalOfUnloadingsAndReturns')->middleware('loggedchecked:loadUpdateModalOfUnloadingsAndReturns');
Route::post('/updateReturns', 'VehicleManagementController@updateReturns')->middleware('loggedchecked:updateReturns');
Route::post('/updateUnloadings', 'VehicleManagementController@updateUnloadings')->middleware('loggedchecked:updateUnloadings');
// Complete
Route::post('/viewCompleteModal', 'VehicleManagementController@viewCompleteModal')->middleware('loggedchecked:viewCompleteModal');
Route::post('/removeDeliveryProducts', 'VehicleManagementController@removeDeliveryProducts')->middleware('loggedchecked:removeDeliveryProducts');
Route::post('/loadItemsModalUpdate', 'VehicleManagementController@loadItemsModalUpdate')->middleware('loggedchecked:loadItemsModalUpdate');
Route::post('/updateStockQuantities', 'VehicleManagementController@updateStockQuantities')->middleware('loggedchecked:updateStockQuantities');
Route::post('/updateStockRackQuantities', 'VehicleManagementController@updateStockRackQuantities')->middleware('loggedchecked:updateStockRackQuantities');
Route::post('/deleteDelivery', 'VehicleManagementController@deleteDelivery')->middleware('loggedchecked:deleteDelivery');
///////////////////////////////////////////////// VEHICLE CONTROLLER END HERE ////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// PRODUCT CONTROLLER BEGIN HERE //////////////////////////////////////////////////////////////////////////////////////////////////
// Category
Route::get('/adminProductManagement', 'ProductManagementController@adminProductManagementIndex')->middleware('userAuth:adminProductManagement');
Route::get('/adminIngredientManagement', 'ProductManagementController@adminProductIngredientsManagementIndex');
Route::get('/productIngredients/{product}', 'ProductManagementController@getProductIngredients')->middleware('loggedchecked:getProductIngredients');
Route::get('/adminProductRegistration', 'ProductManagementController@adminProductRegistrationIndex')->middleware('userAuth:adminProductRegistration');
Route::get('/adminCategoryVariationManagement', 'ProductManagementController@adminCategoryVariationManagementIndex')->middleware('userAuth:adminCategoryVariationManagement');
Route::post('/saveMainCategory', 'ProductManagementController@saveMainCategory')->middleware('loggedchecked:saveMainCategory');
Route::post('/loadCategoryDataToModal', 'ProductManagementController@loadCategoryDataToModal')->middleware('loggedchecked:loadCategoryDataToModal');
Route::post('/updateMainCategory', 'ProductManagementController@updateMainCategory')->middleware('loggedchecked:updateMainCategory');
Route::post('/loadSubCategories', 'ProductManagementController@loadSubCategories')->middleware('loggedchecked:loadSubCategories');
Route::post('/loadProductDetails', 'ProductManagementController@loadProductDetails')->middleware('loggedchecked:loadProductDetails');
// Sub-Category (PRODUCT)
Route::post('/saveSubCategory', 'ProductManagementController@saveSubCategory')->middleware('loggedchecked:saveSubCategory');
Route::post('/updateSubCategory', 'ProductManagementController@updateSubCategory')->middleware('loggedchecked:updateSubCategory');
Route::post('/subCatogoryStatusChange', 'ProductManagementController@subCatogoryStatusChange')->middleware('loggedchecked:subCatogoryStatusChange');
// Variations
Route::post('/saveVariation', 'ProductManagementController@saveVariation')->middleware('loggedchecked:saveVariation');
Route::post('/saveVariationValue', 'ProductManagementController@saveVariationValue')->middleware('loggedchecked:saveVariationValue');
Route::post('/updateVariation', 'ProductManagementController@updateVariation')->middleware('loggedchecked:updateVariation');
Route::post('/updateVariationValue', 'ProductManagementController@updateVariationValue')->middleware('loggedchecked:updateVariationValue');
Route::post('/deleteVariation', 'ProductManagementController@deleteVariation')->middleware('loggedchecked:deleteVariation');
Route::post('/deleteVariationValue', 'ProductManagementController@deleteVariationValue')->middleware('loggedchecked:deleteVariationValue');
Route::post('/toggleVariationStatus', 'ProductManagementController@toggleVariationStatus')->middleware('loggedchecked:toggleVariationStatus');
Route::post('/toggleVariationValueStatus', 'ProductManagementController@toggleVariationValueStatus')->middleware('loggedchecked:toggleVariationValueStatus');
Route::post('/getVariationValues', 'ProductManagementController@getVariationValues')->middleware('loggedchecked:getVariationValues');
Route::post('/getVariationValue', 'ProductManagementController@getVariationValue')->middleware('loggedchecked:getVariationValue');
Route::post('/loadSubCategoriesByMainCategory', 'ProductManagementController@loadSubCategoriesByMainCategory')->middleware('loggedchecked:loadSubCategoriesByMainCategory');
Route::post('/loadVariationValuesByVariation', 'ProductManagementController@loadVariationValuesByVariation')->middleware('loggedchecked:loadVariationValuesByVariation');
Route::post('/saveProductItems', 'ProductManagementController@saveProductItems')->middleware('loggedchecked:saveProductItems');
Route::post('/saveProductIngredients', 'ProductManagementController@saveProductIngredients')->middleware('loggedchecked:saveProductIngredients');
///////////////////////////////////////////////// PRODUCT CONTROLLER END HERE ////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// SALES REP CONTROLLER BEGIN HERE ////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminSalesRepManagement', 'SalesRepManagementController@adminSalesRepManagementIndex')->middleware('userAuth:adminSalesRepManagement');
Route::post('/saveSaleRep', 'SalesRepManagementController@saveSaleRep')->middleware('loggedchecked:saveSaleRep');
Route::post('/loadSaleRepDataToModal', 'SalesRepManagementController@loadSaleRepDataToModal')->middleware('loggedchecked:loadSaleRepDataToModal');
Route::post('/updateSaleRep', 'SalesRepManagementController@updateSaleRep')->middleware('loggedchecked:updateSaleRep');
Route::get('/deleteSalesRep/{id}', 'SalesRepManagementController@deleteSalesRep')->middleware('loggedchecked:deleteSalesRep');
///////////////////////////////////////////////// SALES REP CONTROLLER END HERE //////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// DRIVER MANAGEMENT CONTROLLER BEGIN HERE ////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminDriverManagement', 'DriverManagementController@adminDriverManagementIndex')->middleware('userAuth:adminDriverManagement');
Route::post('/saveDriver', 'DriverManagementController@saveDriver')->middleware('loggedchecked:saveDriver');
Route::post('/loadDriverDataToModal', 'DriverManagementController@loadDriverDataToModal')->middleware('loggedchecked:loadDriverDataToModal');
Route::post('/updateDriver', 'DriverManagementController@updateDriver')->middleware('loggedchecked:updateDriver');
Route::get('/deleteDriver/{id}', 'DriverManagementController@deleteDriver')->middleware('loggedchecked:deleteDriver');
///////////////////////////////////////////////// DRIVER MANAGEMENT CONTROLLER END HERE /////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// PRIVILAGE MANAGEMENT CONTROLLER BEGIN HERE ////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminPrivilageManagement', 'PrivilageamanagementController@adminPrivilageManagementIndex')->middleware('userAuth:adminPrivilageManagement');
Route::post('/loadInterfacesToInterfaceTopics', 'PrivilageamanagementController@loadInterfacesToInterfaceTopics')->middleware('loggedchecked:loadInterfacesToInterfaceTopics');
Route::post('/loadComponentsToInteface', 'PrivilageamanagementController@loadComponentsToInteface')->middleware('loggedchecked:loadComponentsToInteface');
Route::post('/saveDeleteUserRoleComponent', 'PrivilageamanagementController@saveDeleteUserRoleComponent')->middleware('loggedchecked:saveDeleteUserRoleComponent');
///////////////////////////////////////////////// PRIVILAGE MANAGEMENT CONTROLLER END HERE /////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// RAW MATERIAL CONTROLLER BEGIN HERE ///////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminRawMaterialManagement', 'RawMatrialController@adminRawMaterialManagementIndex')->middleware('userAuth:adminRawMaterialManagement');
Route::post('/loadMaterialDataToModal', 'RawMatrialController@loadMaterialDataToModal')->middleware('loggedchecked:loadMaterialDataToModal');
Route::post('/saveMaterial', 'RawMatrialController@saveMaterial')->middleware('loggedchecked:saveMaterial');
Route::post('/updateMaterial', 'RawMatrialController@updateMaterial')->middleware('loggedchecked:updateMaterial');
Route::post('/loadReorderNotifications', 'RawMatrialController@loadReorderNotifications')->middleware('loggedchecked:loadReorderNotifications');
Route::get('/adminLoadReorderProducts', 'RawMatrialController@loadReorderProducts');
Route::post('/updateRawQuanity', 'RawMatrialController@updateRawQuanity')->middleware('loggedchecked:updateRawQuanity');
Route::post('/updateMaterialQtyNext', 'RawMatrialController@updateMaterialQtyNext')->middleware('loggedchecked:updateMaterialQtyNext');
Route::post('/loadQuantityUpdateModalMaerials', 'RawMatrialController@loadQuantityUpdateModalMaerials')->middleware('loggedchecked:loadQuantityUpdateModalMaerials');
///////////////////////////////////////////////// RAW MATERIAL CONTROLLER END HERE /////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// STOCK CONTROLLER BEGIN HERE //////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminStockIn', 'StockController@adminStockInIndex')->middleware('userAuth:adminStockIn');
Route::post('/saveStock', 'StockController@saveStock')->middleware('loggedchecked:saveStock');
Route::post('/loadstockUpdateData', 'StockController@loadstockUpdateData')->middleware('loggedchecked:loadstockUpdateData');
Route::post('/updateStockData', 'StockController@updateStockData')->middleware('loggedchecked:updateStockData');
Route::post('/searchByDateStockIn', 'StockController@searchByDateStockIn')->middleware('loggedchecked:searchByDateStockIn');

Route::get('/adminManageProducts', 'StockController@adminManageProductsIndex')->middleware('userAuth:adminManageProducts');
Route::post('/loadProductsToCategory', 'StockController@loadProductsToCategory')->middleware('loggedchecked:loadProductsToCategory');
Route::post('/loadProductBatches', 'StockController@loadProductBatches')->middleware('loggedchecked:loadProductBatches');
Route::post('/loadQuantityUpdateModal', 'StockController@loadQuantityUpdateModal')->middleware('loggedchecked:loadQuantityUpdateModal');
Route::post('/updateStockBatch', 'StockController@updateStockBatch')->middleware('loggedchecked:updateStockBatch');
Route::post('/updateStockRackCount', 'StockController@updateStockRackCount')->middleware('loggedchecked:updateStockRackCount');
Route::post('/viewStoreRackModel', 'StockController@viewStoreRackModel')->middleware('loggedchecked:viewStoreRackModel');
///////////////////////////////////////////////// STOCK CONTROLLER END HERE ////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// INVOICE CONTROLLER BEGIN HERE ////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminCreateInvoice', 'InvoiceController@adminCreateInvoiceIndex')->middleware('userAuth:adminCreateInvoice');
// Create Invoice - NEW
Route::get('/adminCreateInvoiceNew', 'InvoiceController@adminCreateInvoiceNewIndex')->middleware('userAuth:adminCreateInvoiceNew');
Route::post('/loadInvoiceDeliveryDataToTBL', 'InvoiceController@loadInvoiceDeliveryDataToTBL')->middleware('loggedchecked:loadInvoiceDeliveryDataToTBL');
Route::post('/saveInvoiceData', 'InvoiceController@saveInvoiceData')->middleware('loggedchecked:saveInvoiceData');
Route::post('/loadInvoicesToCustomer', 'InvoiceController@loadInvoicesToCustomer')->middleware('loggedchecked:loadInvoicesToCustomer');
Route::post('/loadInvoiceData', 'InvoiceController@loadInvoiceData')->middleware('loggedchecked:loadInvoiceData');
Route::post('/adminInvoicePrint', 'InvoiceController@adminInvoicePrint')->middleware('loggedchecked:adminInvoicePrint');
Route::post('/viewReturnModal', 'InvoiceController@viewReturnModal')->middleware('loggedchecked:viewReturnModal');

Route::get('/loadInvoicePrintPage/{id}', 'InvoiceController@loadInvoicePrintPage')->middleware('loggedchecked:loadInvoicePrintPage');
Route::post('/checkCreditBilAvailability', 'InvoiceController@checkCreditBilAvailability')->middleware('loggedchecked:checkCreditBilAvailability');
Route::post('/loadCustomercreditModal', 'InvoiceController@loadCustomercreditModal')->middleware('loggedchecked:loadCustomercreditModal');
Route::post('/loadAllCreditBillstoTBL', 'InvoiceController@loadAllCreditBillstoTBL')->middleware('loggedchecked:loadAllCreditBillstoTBL');
Route::post('/validateInvoiceType', 'InvoiceController@validateInvoiceType')->middleware('loggedchecked:validateInvoiceType');
Route::post('/loadInvoicePaymentDetais', 'InvoiceController@loadInvoicePaymentDetais')->middleware('loggedchecked:loadInvoicePaymentDetais');
Route::post('/getInvoices', 'InvoiceController@getInvoices')->middleware('loggedchecked:getInvoices');
Route::get('/adminviewInvoices', 'InvoiceController@adminviewInvoicesIndex')->middleware('userAuth:adminviewInvoices');
Route::post('/removeInvoice', 'InvoiceController@removeInvoice')->middleware('loggedchecked:removeInvoice');
Route::post('/loadInvicesDataToModal', 'InvoiceController@loadInvicesDataToModal')->middleware('loggedchecked:loadInvicesDataToModal');
Route::post('/loadGenerateInoviceModal', 'InvoiceController@loadGenerateInoviceModal')->middleware('loggedchecked:loadGenerateInoviceModal');
// For Temporary Use ONLY
Route::get('/addMissingPaymentForInvoices', 'InvoiceController@addMissingPaymentForInvoices')->middleware('loggedchecked:addMissingPaymentForInvoices');

Route::get('/adminOrderTakeForm', 'InvoiceController@adminOrderTakeFormIndex')->middleware('loggedchecked:adminOrderTakeFormIndex');
Route::post('/saveOrder', 'InvoiceController@saveOrder')->name('saveOrder')->middleware('loggedchecked:saveOrder');
Route::get('/adminOrderTakeFormDetails', 'InvoiceController@adminOrderTakeFormDetailsIndex')->middleware('loggedchecked:adminOrderTakeFormDetailsIndex');
Route::post('/checkExistingOrder', 'InvoiceController@checkExistingOrder')->name('checkExistingOrder')->middleware('loggedchecked:checkExistingOrder');
Route::post('/checkOrderDetails', 'InvoiceController@checkOrderDetails')->name('checkOrderDetails')->middleware('loggedchecked:checkOrderDetails');

Route::post('/loadOrderDetailsAll', 'InvoiceController@loadOrderDetailsAll')->name('loadOrderDetailsAll');
Route::post('/loadOrderDetailsSingle', 'InvoiceController@loadOrderDetailsSingle')->name('loadOrderDetailsSingle');

///////////////////////////////////////////////// INVOICE CONTROLLER END HERE /////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// PAYMENT CONTROLLER BEGIN HERE ///////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminPayment', 'PaymentController@adminPaymentIndex')->middleware('userAuth:adminPayment');
Route::post('/saveInvoicePayment', 'PaymentController@saveInvoicePayment')->middleware('loggedchecked:saveInvoicePayment');
Route::post('/removeInvoicePayment', 'PaymentController@removeInvoicePayment')->middleware('loggedchecked:removeInvoicePayment');
Route::post('/addCreditPayment', 'PaymentController@addCreditPayment')->middleware('loggedchecked:addCreditPayment');
Route::post('/searchInvoices', 'PaymentController@searchInvoices')->middleware('loggedchecked:searchInvoices');
Route::get('/adminPaymentReverse', 'PaymentController@adminPaymentReverseIndex')->middleware('userAuth:adminPaymentReverse');
Route::post('/reversePayment', 'PaymentController@reversePayment')->middleware('loggedchecked:reversePayment');
///////////////////////////////////////////////// PAYMENT CONTROLLER END HERE /////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// REPORT CONTROLLER BEGIN HERE ////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminStockReport', 'ReportController@adminStockReportIndex')->middleware('userAuth:adminStockReport');
Route::post('/getStockReport', 'ReportController@getStockReport')->middleware('loggedchecked:getStockReport');

Route::get('/adminSalesReport', 'ReportController@adminSalesReportIndex')->middleware('userAuth:adminSalesReport');
Route::get('/adminSalesReport2', 'ReportController@adminSalesReport2Index')->middleware('userAuth:adminSalesReport2');
Route::post('/getSalesReport', 'ReportController@getSalesReport')->middleware('loggedchecked:getSalesReport');
Route::post('/getSalesReport2', 'ReportController@getSalesReport2')->middleware('loggedchecked:getSalesReport2');
Route::post('/loadInvoicePaymentHistory', 'ReportController@loadInvoicePaymentHistory')->middleware('loggedchecked:loadInvoicePaymentHistory');

Route::get('/adminRejectedInvoiceReport', 'ReportController@adminRejectedInvoiceReport')->middleware('userAuth:adminRejectedInvoiceReport');
Route::post('/getRejectedInvoiceReport', 'ReportController@getRejectedInvoiceReport')->middleware('loggedchecked:getRejectedInvoiceReport');
Route::post('/loadRejectedInvoiceDataModal', 'InvoiceController@loadRejectedInvoiceDataModal')->middleware('loggedchecked:loadRejectedInvoiceDataModal');

Route::get('/adminStockInReport', 'ReportController@adminStockInReportIndex')->middleware('userAuth:adminStockInReport');
Route::get('/adminDailySalesReport', 'ReportController@adminDailySalesReport')->middleware('userAuth:adminDailySalesReport');
Route::post('/getSalesReportDaily', 'ReportController@getSalesReportDaily')->middleware('loggedchecked:getSalesReportDaily');

Route::get('/adminCollectionReport', 'ReportController@adminCollectionReportIndex')->middleware('userAuth:adminCollectionReport');
Route::post('/getCollectionReport', 'ReportController@getCollectionReport')->middleware('loggedchecked:getCollectionReport');
Route::get('/adminRouteWiseCreditReport', 'ReportController@adminRouteWiseCreditReport')->middleware('userAuth:adminRouteWiseCreditReport');

Route::post('/getCreditRouteReport', 'ReportController@getCreditRouteReport')->middleware('loggedchecked:getCreditRouteReport');
Route::get('/loadCreditReportPrint/{dateFrom}/{dateTo}/{route}', 'ReportController@loadCreditReportPrint')->middleware('loggedchecked:loadCreditReportPrint');
Route::get('/print/{dateFromFormat}/{customer}/{invoiceType}/{salesRep}/{drivers}/{vehicle}/{dateToFormat}', 'ReportController@print')->middleware('loggedchecked:print');

// Route Wise Sales Report
Route::get('/adminRouteWiseSalesReport', 'ReportController@adminRouteWiseSalesReportIndex')->middleware('userAuth:adminRouteWiseSalesReport');
Route::post('/getRouteWiseSalesReport', 'ReportController@getRouteWiseSalesReport')->middleware('loggedchecked:getRouteWiseSalesReport');
Route::post('/viewInvoiceListModal', 'ReportController@viewInvoiceListModal')->middleware('loggedchecked:viewInvoiceListModal');

// Discount Report
Route::get('/adminDiscountReport', 'ReportController@adminDiscountReportIndex')->middleware('userAuth:adminDiscountReport');
Route::post('/getDiscountReport', 'ReportController@getDiscountReport')->middleware('loggedchecked:getDiscountReport');
///////////////////////////////////////////////// REPORT CONTROLLER END HERE //////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// ROUTE CONTROLLER BEGIN HERE /////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminCreateRoute', 'RouteController@adminCreateRouteIndex')->middleware('userAuth:adminCreateRoute');
Route::post('/saveRoute', 'RouteController@saveRoute')->middleware('loggedchecked:saveRoute');
Route::post('/loadRouteData', 'RouteController@loadRouteData')->middleware('loggedchecked:loadRouteData');
Route::post('/updateRoute', 'RouteController@updateRoute')->middleware('loggedchecked:updateRoute');
Route::get('/deleteRoute/{id}', 'RouteController@deleteRoute')->middleware('loggedchecked:deleteRoute');
Route::post('/searchShopsToRoute', 'RouteController@searchShopsToRoute')->middleware('loggedchecked:searchShopsToRoute');
///////////////////////////////////////////////// ROUTE CONTROLLER END HERE ///////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// SETTINGS CONTROLLER BEGIN HERE ////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/commission-settings', 'SettingsController@indexCommissionSettings')->middleware('userAuth:commission-settings');
route::post('/commission-settings/saveCommissionSettings', 'SettingsController@saveCommissionSettings')->middleware('loggedchecked:saveCommissionSettings')->name('saveCommissionSettings');
route::post('/commission-settings/loadCommissionSettingsToUpdateModal', 'SettingsController@loadCommissionSettingsToUpdateModal')->middleware('loggedchecked:loadCommissionSettingsToUpdateModal')->name('loadCommissionSettingsToUpdateModal');
route::post('/commission-settings/updateCommissionSettings', 'SettingsController@updateCommissionSettings')->middleware('loggedchecked:updateCommissionSettings')->name('updateCommissionSettings');
route::post('/commission-settings/statusChangeCommissionSettings', 'SettingsController@statusChangeCommissionSettings')->middleware('loggedchecked:statusChangeCommissionSettings')->name('statusChangeCommissionSettings');
///////////////////////////////////////////////// SETTINGS CONTROLLER END HERE ///////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////// SALARY CONTROLLER BEGIN HERE ////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('/adminGenerateSalarySlips', 'SalarySlipController@adminGenerateSalarySlips')->middleware('userAuth:adminGenerateSalarySlips');
Route::post('/generateSalarySlip', 'SalarySlipController@generateSalarySlip')->middleware('loggedchecked:generateSalarySlip');
// old
Route::get('/printSalarySlip/{sales}/{driver}/{dateFromFormat}/{dateToFormat}', 'SalarySlipController@printSalarySlip')->middleware('loggedchecked:printSalarySlip');
// new
Route::get('/printGeneratedSalarySlip/{userName}/{companyWorkingDay_count}/{employeeWorkedDay_count}/{monthName}/{attendanceBonus}/{payment}/{commission}/{specialSalesCommission}/{totalUnpaidCreditBillAmount}/{totalPayableSalary}', 'SalarySlipController@printGeneratedSalarySlip')->middleware('loggedchecked:printGeneratedSalarySlip');
///////////////////////////////////////////////// SALARY CONTROLLER END HERE ///////////////////////////////////////////////////////////////////////////////////////////////////


//Order Management
Route::get('/adminOrderManagement', 'OrderManagementController@adminOrderManagementIndex')->middleware('userAuth:adminOrderManagement');
Route::get('/createOrders', 'OrderManagementController@createOrders')->name('createOrders')->middleware('userAuth:createOrders');


//Stock Management
Route::get('/adminStockManagement', 'StockManagementController@adminStockManagementIndex')->name('adminStockManagementIndex')->middleware('userAuth:adminStockManagement');
Route::get('/admingrn', 'StockManagementController@admingrn')->name('admingrn')->middleware('userAuth:admingrn');

//Dashboard Management
Route::get('/adminDashboard', 'DashboardManagementController@adminDashboard')->name('adminDashboard')->middleware('userAuth:adminDashboard');


//Pos Management
Route::get('/pos-view', 'PosManagementController@posView')->name('posView');

//Report
Route::get('/adminsalesreport', 'ReportController@adminSalesReportIndex')->name('adminSalesReportIndex')->middleware('userAuth:adminSalesReport');
Route::get('/adminstockreport', 'ReportController@adminStockReportIndex')->name('adminStockReportIndex')->middleware('userAuth:adminStockReport');
