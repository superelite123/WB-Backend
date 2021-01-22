<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

//Auth::routes();
Auth::routes(['verify' => true, 'register' => false]);

//home-dashboard
Route::get('/home', 'HomeController@index')->name('home');
Route::post('get_harvest_chart_data','HomeController@get_harvest_chart_data');
Route::post('get_strain_chart_data','HomeController@get_strain_chart_data');
Route::group( ['middleware' => ['auth','permission:clock_in_out']], function() {
    Route::get('/clocking','HomeController@clocking');
    Route::get('_set_clock_in','HomeController@_set_clock_in');
});
Route::group( ['middleware' => ['auth','permission:distro_cio']], function() {
    Route::get('/clocking_distro','HomeController@clockingDistro');
    Route::get('_set_lunch_in','HomeController@_set_lunch_in');
});
Route::group( ['middleware' => ['auth','permission:Administration']], function() {
    Route::resource('users', 'UserController');
    Route::resource('roles', 'RoleController');
    Route::get('edit_role/{id}','RoleController@edit');
    Route::get('_delete_role/{id}','RoleController@delete');
    Route::get('permissions','PermissionController@index');
    Route::post('_save_permission','PermissionController@save');
    Route::get('_delete_permission/{id}','PermissionController@delete');
});

Route::group( ['middleware' => ['auth','permission:time_keeping']], function() {
    Route::get('clocking_report','HomeController@clocking_report');
    Route::get('clocking_lunch_report','HomeController@clocking_lunch_report');
    Route::get('_delete_distro_log','HomeController@deleteDistroLog');
    Route::post('_get_clocking_data','HomeController@_get_clocking_data');
    Route::post('_get_clocking_lunch_data','HomeController@_get_clocking_lunch_data');
    Route::post('_change_time_range','HomeController@_change_time_range');
    Route::post('_get_clocking_chart_data','HomeController@_get_clocking_chart_data');
});
Route::group( ['middleware' => 'auth'], function(){
    Route::any('producttypes', 'CC@producttypes')->middleware(['permission:product_type']);
    Route::any('batch', 'CC@batch');
    Route::any('fginventory', 'CC@fginventory')->middleware(['permission:inventory_fg']);
    Route::any('vaultinventory', 'CC@vaultinventory')->middleware(['permission:inventory_fg']);
    Route::any('coalibrary', 'CC@coalibrary')->middleware(['permission:coa_upload']);
    Route::any('inventorytype', 'CC@inventorytype');
    Route::any('inventorycategory', 'CC@inventorycategory');
    Route::any('contactperson', 'CC@contactperson');
    Route::any('contacttype', 'CC@contacttype');
    Route::any('ospmatrix', 'CC@ospmatrix');
    Route::any('licensetype', 'CC@licensetype');
    Route::any('status', 'CC@status');
    Route::any('terms', 'CC@terms');
    Route::any('units', 'CC@units');
    Route::post('assets_generate','CC@assets_generate');
    Route::any('upccontroller', 'CC@upccontroller');
    Route::any('harvestdata', 'CC@harvestdata');
    Route::any('harvestover', 'CC@harvestover')->middleware(['permission:Administration']);
    Route::any('fgmodifystatus', 'CC@fgmodifystatus')->middleware(['permission:Administration']);
    Route::any('vaultmodifystatus', 'CC@vaultmodifystatus')->middleware(['permission:Administration']);
    Route::any('harvestitem', 'CC@harvestitem');
    Route::any('descriptions', 'CC@descriptions');
    Route::post('get_tracker_list', 'CC@get_tracker_list');
    Route::any('asset','CC@assets');
    Route::any('delivery_method','CC@delivery_method');
    Route::any('asset_potal','CC@asset_potal');
    Route::post("_asset_potal_get_assets",'CC@_asset_potal_get_assets');

    //harvestDynamics
    Route::any('harvestdynamics', 'CC@harvestdynamics');
    Route::any('_sendToDryWeightlist', 'CC@_sendToDryWeightlist');
    //holding Inventory
    Route::any('holdingInventory', 'CC@holdingInventory');
    Route::post("sendHoldingToFG",'CC@sendHoldingToFG');
    Route::any('credit_reasons','CC@credit_reasons');
    Route::post("sendHoldingToVault",'CC@sendHoldingToVault');
    Route::post("_checkUpc",'CC@_checkUpc');
    //harvestBuilder
    Route::any('allocationresults', 'CC@allocationresults');
    Route::any('waste','CC@waste');
    Route::any('allocationbuilder','CC@allocationbuilder');
    Route::post("_harvestTrackerBuilerBarcode",'CC@_harvestTrackerBuilerBarcode');
    Route::post("_harvestTrackerBuilerToHoldingInventory",'CC@_harvestTrackerBuilerToHoldingInventory');
});
//Processing Control
Route::group( ['middleware' => ['auth','permission:ps_ctl_vendor']], function(){
    Route::any('vendors', 'CC@vendors');
    Route::any('cultivator', 'CC@cultivator');
    Route::any('distributor', 'CC@distributor');
    Route::any('ospfacility', 'CC@ospfacility');
    Route::any('strainname', 'CC@strainname');
});

//Location Area
Route::group( ['middleware' => ['auth','permission:location_cart']], function(){
    Route::any('locationarea', 'CCMulti@locationarea');
    Route::any('locationcart', 'CCMulti@locationcart');
    Route::any('locationshelf', 'CCMulti@locationshelf');
});

//Customer Relations
Route::group( ['middleware' => ['auth']], function(){
    Route::any('customers', 'CC@customers')->middleware(['permission:c_relations_clients']);
    Route::any('customers2', 'CC@customers2')->middleware(['permission:c_relations_clients']);
    Route::any('clients', 'CC@clients')->middleware(['permission:c_relations_person']);
    Route::any('pricematrix', 'CC@pricematrix')->middleware(['permission:c_relations_price_matrix']);
});

//harvest
Route::group( ['prefix' => 'harvest','middleware' => 'auth'], function(){
    Route::get('create','HarvestController@create');
    Route::get('edit','HarvestController@create');
    Route::get('list','HarvestController@list');
    Route::get('list_admin','HarvestController@list');
    Route::any('form_fresh','HarvestController@form_fresh');
    Route::post('store_fresh','HarvestController@store_fresh');
    Route::get('dry','HarvestController@form_dry');
    Route::get('list_dry','HarvestController@list_dry');
    Route::get('statistics','HarvestController@statistic');

    Route::get('list_archived','HarvestController@list_archived');
    Route::post('_deduct_waist','HarvestController@_deduct_waist');
    //curning
    Route::get('curning','HarvestController@curning');
    Route::post('get_curning_table_data','HarvestController@get_curning_table_data');
    Route::post('_dry_build_one','HarvestController@_dry_build_one');
    Route::post('_curning_harvest_barcode','HarvestController@_curning_harvest_barcode');
    Route::post('_curning_to_holding','HarvestController@_curning_to_holding');
    Route::post('_get_curning_barcode','HarvestController@_get_curning_barcode');
    Route::get('form_curning_asset','HarvestController@form_curning_asset');
    Route::post('store_curning_asset','HarvestController@store_curning_asset');
    Route::post('_list_harvest_barcode','HarvestController@_list_harvest_barcode');
    Route::get('process_history','HarvestController@process_history');
    Route::post('get_process_history_data','HarvestController@get_process_history_data');
    Route::post('_throw_curing','HarvestController@_throw_curing');
    //history
    Route::get('history','HarvestController@history');
    Route::post('get_history_data','HarvestController@get_history_data');

    Route::post('store','HarvestController@store');
    Route::post('store_dry','HarvestController@store_dry');
    Route::post('update','HarvestController@update');
    Route::post('delete_harvest','HarvestController@delete');
    Route::post('items','HarvestController@items');
    Route::post('get_harvest_table_data','HarvestController@get_harvest_table_data');
    Route::post('get_harvest_dry_table_data','HarvestController@get_harvest_dry_table_data');
    Route::post('get_harvest_archived_table_data','HarvestController@get_harvest_archived_table_data');
    Route::post('plattag_unique','HarvestController@plattag_unique');
    Route::post('check_existing_record','HarvestController@check_existing_record');
    Route::post('send_dynamcis','HarvestController@send_dynamcis');

    Route::post('saverow','HarvestController@saverow');
    Route::get('abc','HarvestController@abc');

    Route::any('dashboard','HarvestController@dashboard');

    /*
        @desc Transfer Page
        @GET
        @POST
    */

    Route::get('transfer','HarvestController@form_transfer');
    Route::post('store_transfer','HarvestController@store_transfer');
    Route::post('get_item_from_barcode','HarvestController@get_item_from_barcode');
    Route::get('transfer_history','HarvestController@transfer_history');
    Route::post('get_transfer_history_table_data','HarvestController@get_transfer_history_table_data');

    /**
     * Plant Room Builder
     */
    Route::get('room_builder','HarvestController@room_builder');
    Route::post('_get_room_builder_table_data','HarvestController@_get_room_builder_table_data');
    Route::get('form_room_builder','HarvestController@form_room_builder');
    Route::post('store_room_builder','HarvestController@store_room_builder');
    Route::get('statistic','HarvestController@statistic');
    Route::post('_check_room_available','HarvestController@_check_room_available');
});
/**
 * 3.29
 * WB New Po
 */
Route::group( ['prefix' => 'order','middleware' => ['auth','permission:order_new']],function(){
    Route::get('form','OrderController@form');
    Route::post('store','OrderController@store');
    Route::post('_form_customer_list','OrderController@_form_customer_list');
    Route::post('_form_avaliable_qty','OrderController@_form_avaliable_qty');
    Route::post('_customer_credit_note_total','OrderController@_CustomerCreditNoteTotal');
});
/**
 * 3.29
 * WB Pending PO
 */
Route::group( ['prefix' => 'order','middleware' => ['auth','permission:order_pending_list']],function(){
    Route::get('pending_list','OrderController@pending_list');
    Route::post('get_pending_list','OrderController@get_pending_list');
    Route::post('_getPOrderCustomerDetail','OrderController@_pendingOrderCustomerDetail');
    Route::get('pending_detail/{id}/{print}','OrderController@pending_detail');
    Route::any('_pending_email','OrderController@_pending_email');
    Route::any('_set_priority','OrderController@_set_priority');
    Route::post('_send_pending_fulfillment','OrderController@_send_pending_fulfillment');
    Route::any('_remove_order','OrderController@_remove_order');
    /**
     * Add Discount
     * 5.19
     */
    Route::post('_add_discount','OrderController@_AddDiscount');
});
/**
 * 3.29
 * Walnut FulFillment
 */
Route::group( ['prefix' => 'order','middleware' => ['auth','permission:order_fulfillment_list']],function(){
    //Fulfillment List part
    Route::get('fulfillment_list','OrderController@fulfillment_list');
    Route::post('get_fulfillment_list','OrderController@get_fulfillment_list');
    Route::post('get_fulfillment_problematic_list','OrderController@get_fulfillment_problematic_list');
    Route::get('fulfilled_print_from_form/{id}','OrderFulFilledController@barcode_print');
    Route::get('fulfillment_detail/{id}/{print}','OrderController@pending_detail');
    //Orders have problem in Fulfillment
    Route::post('_send_problem_salesPerson','OrderController@_send_problem_salesPerson');

    //Fulfillment Form part
    Route::get('fulfillment_form','OrderController@fulfillment_form');
    Route::post('_check_metrc_info','OrderController@_check_metrc_info');
    Route::post('_fulfillment_store','OrderController@_fulfillment_store');
    Route::post('_print_barcode','OrderController@_print_barcode');
    Route::get('fulfilled_print_from_form/{id}','OrderFulFilledController@barcode_print');
});
/**
 * Walnut to Deliver
 */
Route::group( [ 'prefix' => 'order_fulfilled','middleware' => ['auth','permission:order_fulfilled_list']],function(){

    Route::get('home','OrderFulFilledController@home');
    Route::get('scheduled','OrderScheduledController@index');
    Route::post('_chage_order_delivery_date','OrderScheduledController@changeDate');
    Route::get('get_calendar_request','OrderScheduledController@_getCalendarRequest');
    Route::get('_delete_schedule','OrderScheduledController@deleteDate');
    Route::post('get_fulfilled_list','OrderFulFilledController@get_list');
    Route::post('get_reject_list','OrderFulFilledController@get_reject_list');
    Route::post('_fulfilled_email','OrderFulFilledController@_email');
    Route::post('registerDeliverySchedule','OrderFulFilledController@registerDeliverySchedule');
    Route::get('view/{id}/{print}','OrderFulFilledController@view');

    /**
     * Restocking Order
     */
    Route::get('_restock_order/{id}','OrderFulFilledController@_restockOrder');
    Route::get('_complete_rejection/{id}','OrderFulFilledController@_completeRejection');

    /**
     * 6.12
     */
    Route::post('_set_metrc_manifest','OrderFulFilledController@_setMetrcManifest');
    Route::get('_complete_rejection/{id}','OrderFulFilledController@_completeRejection');
    //Payment Page
    Route::get('payment/{id}','OrderFulFilledController@collectPayment');
    Route::get('payment/_delete_payment/{id}','SignController@_deletePayment');
    Route::post('payment/_collect_money','SignController@_collect_money');
    //fulfilled edit
    Route::get('edit/{id}','OrderFulFilledController@edit')->permission('order_edit');
    Route::get('edit/_check_metrc_info/{metrc}','OrderFulFilledController@_check_metrc_info');
    Route::post('edit/_store','OrderFulFilledController@_edit_store');
    Route::post('edit/_update_top_info','OrderFulFilledController@_update_top_info');

    Route::get('delete/{id}','OrderFulFilledController@delete')->permission('order_edit');
    Route::post('delete/_delete_store','OrderFulFilledController@_delete_store');

    Route::get('barcode_print/{id}','OrderFulFilledController@barcode_print');
    Route::post('_email','OrderFulFilledController@_email');
    Route::get('_download_invoice_pdf/{id}','OrderFulFilledController@_download_invoice_pdf');
    Route::post('_email_requirment_check','OrderFulFilledController@_email_requirment_check');
    Route::post('_set_paid_order','OrderFulFilledController@_set_paid_order');
    Route::post('_set_metrc_ready_order','OrderFulFilledController@_set_metrc_ready_order');
    Route::post('_set_coainbox_order','OrderFulFilledController@_set_coainbox_order');
    Route::post('_set_distributor','OrderFulFilledController@_set_distributor');
    Route::post('_set_m_manifest','OrderFulFilledController@_set_m_manifest');

    //print from fulfillment Form
    Route::get('fulfilled_print_from_form/{id}','OrderFulFilledController@barcode_print');
    //archived part
    Route::get('delivered','OrderFulFilledController@delivered');

    /**
     * 7.9
     */
   Route::get('i_report','InvoiceReportController@index');
   Route::post('i_report/get_list','InvoiceReportController@get_list');

});
/**
 * 3.29
 * Signature & Delivery
 */
Route::group( ['prefix' => 'signature','middleware' => ['auth','permission:order_sign_list']],function(){
    //home list
    Route::get('/home','SignController@index');
    Route::post('get_signature_list','SignController@get_list');
    Route::post('_set_d_status','SignController@_set_d_status');
    Route::post('_save_deliver_note','SignController@_save_deliver_note');
    Route::post('_send_sales_email','SignController@_sendSalesEmail');
    //panel
    Route::get('panel/{id}','SignController@Panel');
    Route::post('panel/_save_sign','SignController@_save_sign');
    Route::get('panel/_delete_payment/{id}','SignController@_deletePayment');
    Route::post('panel/_collect_money','SignController@_collect_money');
});
/**
 * 3.29
 * Delivered List
 */
Route::group( [ 'prefix' => 'order_fulfilled','middleware' => ['auth','permission:order_delivered']],function(){
    Route::get('delivered','OrderFulFilledController@delivered');
    Route::post('get_delivered_list','OrderFulFilledController@getDeliveredList');
    Route::post('_set_order_undeliver','OrderFulFilledController@_setUndeliver');
    Route::post('get_csv_list','OrderFulFilledController@get_list');
    //PaymentView
    Route::get('delivered_payment_view/{id}','OrderFulFilledController@deliveredPaymentView');
    Route::post('store_invoice_contact','OrderFulFilledController@storeInvoiceContact');
    //Sign Panel
    Route::get('sign_panel/{id}','SignController@Panel');
    
});
Route::group( [ 'prefix' => 'credit_notes','middleware' => ['auth','permission:order_delivered']],function(){
    /**
     * CreditNote
     */
    //Credit Note
    Route::get('/','CreditNoteController@archive');
    Route::get('create/{id}','CreditNoteController@form');
    Route::get('snap/{id}','CreditNoteController@snap');
    Route::get('download/{id}','CreditNoteController@download');
    Route::get('_delete/{id}','CreditNoteController@delete');
    Route::post('_approve','CreditNoteController@approve');
    Route::post('store','CreditNoteController@store');
    Route::post('email','CreditNoteController@email');
    Route::post('_archives','CreditNoteController@_archives');
});
/**
 * 5.10.Payment Verification
 */
Route::group( ['prefix' => 'order','middleware' => ['auth','permission:order_pverification']],function(){
    Route::get('p_v','OrderFulFilledController@pvHome')->middleware(['auth']);
    Route::get('_verify_payment/{iid}/{pid}/{amount}','SignController@_verifyPayment');
});
/**
 * 3.29
 * AR Calender
 * 4.5 Modification
 */
Route::group( ['prefix' => 'signature','middleware' => ['auth','permission:order_arcalender']],function(){
    Route::get('pverification/home','SignController@pVerificationHome');
    Route::post('pverification/_get_list','SignController@_getPVerifications');
    Route::get('pverification/payment_view/{id}','OrderFulFilledController@deliveredPaymentView');
});
/**
 * 3.29
 * Archived List
 */
Route::group( [ 'prefix' => 'order_fulfilled','middleware' => ['auth','permission:order_archived']],function(){
    Route::get('archived','OrderFulFilledController@archived');
    Route::post('get_archived_list','OrderFulFilledController@get_list');
    Route::get('archived_view/{id}/{print}','OrderFulFilledController@view');
});
/**
 * 3.29
 * Walnut Order:Report Page
 */
Route::group( [ 'prefix' => 'order','middleware' => ['auth','permission:order_report']],function(){
    Route::get('report','OrderController@report');
    Route::get('fa_export_log','OrderExportLogController@index');
    Route::post('_get_report_list','OrderController@_get_report_list');
});
Route::get('order_fulfilled/_set_status','OrderFulFilledController@setOrderStatus');

/**
 * 3.29
 * Walnut Financial
 */
Route::group( ['prefix' => 'admin','middleware' => ['auth','permission:order_finacial']],function(){
    Route::get('/financial_export','AdminController@financialExport');
    Route::post('getInvoices','AdminController@getInvoices');
    Route::post('getCustomers','AdminController@getCustomers');
    Route::post('_toggle_exported','AdminController@toggleExported');
    Route::post('_getCustomerInvoice','AdminController@_getCustomerInvoice');
    Route::get('view/{id}/{print}','OrderFulFilledController@view');
    Route::get('_download_invoice_pdf/{id}','OrderFulFilledController@_download_invoice_pdf');
});
Route::any('promo', 'CC@promo')->middleware(['auth','permission:order_promo']);
Route::get('set_order_delivery_status/{id}/{status}','OBaseController@setDeliveryStatus');
Route::group( ['prefix' => 'inventory','middleware' => ['auth']],function(){
    Route::get('combine','InventoryController@combinePanel');
    Route::post('_combine','InventoryController@combineItems');
    Route::get('split','InventoryController@splitPanel');
    Route::post('getInventory','InventoryController@getInventory');
    Route::get('_check_metrc_duplicate','InventoryController@_checkMetrcDuplicate');
    Route::post('_split','InventoryController@splitItem');
    Route::get('import','InventoryController@importPanel');
    Route::post('importInventory','InventoryController@importInventory');
    Route::post('bulk_import_confirm','InventoryController@bulk_import_confirm');
    Route::post('bulk_import','InventoryController@bulk_import');
    Route::get('archive_imported','InventoryController@archive_imported');
    Route::post('_approve_imported','InventoryController@_approve_imported');
    Route::post('_approve_history','InventoryController@_approveHistory');
    Route::get('restock_history','InventoryRestockController@history');
});
/**
 * NDA Management
 */
Route::group( ['prefix' => 'nda_management','middleware' => ['auth','permission:Administration']],function(){
    Route::get('home','NdaManagementController@home');
    Route::get('view/{id}','NdaManagementController@view');
    Route::post('get_ndaLogs','NdaManagementController@getNdaLogs');
    Route::get('_delete_id/{id}','NdaManagementController@deleteID');
});
/**
 * NDA New Landing Page
 */
Route::get('nda_signout_p','GuestController@NDASignoutP')->middleware(['auth','permission:c_relations_clients']);
Route::get('nda_signout/{id}','GuestController@NDASignout')->middleware(['auth','permission:c_relations_clients']);
Route::get('nda_index','GuestController@NDAIndex')->middleware(['auth','permission:c_relations_clients']);
/**
 * 4.10
 * Nda Home Page
 */
Route::get('nda_home','GuestController@NDAHome')->middleware(['auth','permission:c_relations_clients']);
Route::get('_nda_email_check/{email}','GuestController@_NDAEmailCheck');
Route::post('_store_nad_e','GuestController@_storeNDAE');
/**
 * 3.30
 * NDA page
 */
Route::get('nda_page','GuestController@NDAPage')->middleware(['auth','permission:c_relations_clientss']);
Route::post('_store_nda','GuestController@storeNDA');

/**
 * 6.19
 */
Route::get('invrestock','InventoryRestockController@index')->middleware(['auth','permission:fginventory']);
Route::get('get_invrestock','InventoryRestockController@getList')->middleware(['auth','permission:fginventory']);
Route::post('invrestock/approve','InventoryRestockController@approve');

/**
 * Metrc Search Page
 */
Route::get('metrc_search','MetrcTagSearchController@index')->middleware(['auth','permission:fginventory']);
Route::post('metrc_search','MetrcTagSearchController@search')->middleware(['auth','permission:fginventory']);

/**
 * 5.10.Payment Verification
 */
Route::group( ['prefix' => 'sales_report','middleware' => ['auth','permission:order_report']],function(){
    Route::get('/','SalesReportController@index')->name('sreport.month');
});

Route::group( ['prefix' => 'admin','middleware' => ['auth']],function(){
    Route::get('order/index','AdminOrderManagementController@index')->name('admin.order.index');
    Route::get('order/find','AdminOrderManagementController@find')->name('admin.order.find');
});

Route::any('adminfg', 'CC@adminfg')->middleware(['auth','permission:fginventory']);
Route::any('adminig', 'CC@adminig')->middleware(['auth','permission:fginventory']);