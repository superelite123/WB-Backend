<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InvoiceExportLog;
class OrderExportLogController extends Controller
{
    //
    public function index()
    {
        return view('order.fa_export_log',['data' => InvoiceExportLog::orderBy('created_at','desc')->get()]);
    }
}
