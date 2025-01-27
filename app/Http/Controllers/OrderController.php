<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ModeOfPayment;
use App\Models\OrderPaymentHistory;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function view(Request $request)
    {
        $oa = Order::where('oa_number', $request->oa_no)->first();
        $payments = OrderPaymentHistory::where('oa_id', $oa->oa_id)->latest('date_of_payment')->get();
        $initial = OrderPaymentHistory::where('oa_id', $oa->oa_id)->orderBy('date_of_payment')->first();
        $total_paid = OrderPaymentHistory::where('oa_id', $oa->oa_id)->sum('amount');
        $mops = ModeOfPayment::all();
        return view('order-print-preview', compact('oa', 'payments', 'initial', 'total_paid', 'mops'));
    }

    public function update_details(Request $request)
    {
        $oa = Order::where('oa_number', $request->oa_number)->first();
        $oa->current_level = $request->current_level;
        $oa->delivery_date = $request->delivery_date;
        $oa->delivery_time = $request->delivery_time;
        $oa->initial_investment = $request->initial_investment;
        $oa->terms = $request->terms;
        $oa->save();
        return redirect(route('order.agreements.view.print', $oa->oa_number));
    }
}
