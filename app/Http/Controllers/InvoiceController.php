<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function user_wise_invoices(Request $request){

        $query = Invoice::where('admission_no', $request->admission_id)->get();
         
        return $query;
    }
}
