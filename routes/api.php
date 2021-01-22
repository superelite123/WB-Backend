<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'wb','prefix' => 'wb','namespace' => 'WB'],function(){
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});
//Order
// Route::group(['middleware' => 'wb','prefix' => 'wb/order','namespace' => 'WB'],function(){
//     Route::get('form/get_init_data', 'OrderApiController@getInitialData');
//     Route::get('archive0/customer-detail','OrderController@_pendingOrderCustomerDetail');
//     Route::get('detail/{id}', 'OrderApiController@detail');
//     Route::get('archive0', 'OrderApiController@archive0');
//     Route::get('archive1', 'OrderApiController@archive1');
//     Route::get('archive2/f', 'OrderApiController@archive2F');
//     Route::get('archive2/r', 'OrderApiController@archive2R');
//     Route::get('archive4', 'OrderApiController@archive4');
//     Route::get('archive5/invoices', 'OrderApiController@archive5Invoices');
//     Route::get('archive5/clients', 'OrderApiController@archive5Invoices');
// });

// Route::get('wb/customers/relations', function () {
//     return [
//         'contactPersons' => ContactPerson::all(),
//         'licenseTypes' => LicenseType::all(),
//         'terms' => Term::all(),
//         'dayOfWeek' => DayOfWeek::all(),
//         'states' => State::all(),
//         'status' => Status::all(),
//     ];
// });
// Route::apiResource('customers', 'CustomerController');