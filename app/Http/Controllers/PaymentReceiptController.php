<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderPaymentHistory;
use App\Http\Controllers\Controller;

class PaymentReceiptController extends Controller
{
    /**
     * Generate and return payment receipt view for a specific payment
     *
     * @param int $payment_id The payment ID
     * @return \Illuminate\Contracts\View\View
     */
    public function showSingleReceipt($payment_id)
    {
        $payment = OrderPaymentHistory::with('details')->findOrFail($payment_id);

        // If this payment is part of a batch, redirect to the batch receipt view
        if ($payment->isPartOfBatch()) {
            return redirect()->route('receipt.show.batch', [
                'batch_number' => $payment->batch_receipt_number
            ]);
        }

        return view('receipts.payment', compact('payment'));
    }

    /**
 * Show a consolidated receipt for a batch of payments
 *
 * @param string $batch_number The batch receipt number
 * @return \Illuminate\Contracts\View\View
 */
public function showBatchReceipt($batch_number)
{
    $batch_payments = OrderPaymentHistory::where('batch_receipt_number', $batch_number)
        ->orderBy('receipt_sequence')
        ->get();

    if ($batch_payments->isEmpty()) {
        abort(404, 'Batch receipt not found');
    }

    $first_payment = $batch_payments->first();
    $order = $first_payment->details;
    $batch_total = $batch_payments->sum('amount');

    // Get all payment modes used in this batch
    $payment_modes = $batch_payments->pluck('mop')->unique()->toArray();

    return view('receipts.batch', compact(
        'batch_payments',
        'order',
        'batch_number',  // <-- This variable name should match what's used in the view
        'batch_total',
        'payment_modes'
    ));
}
    /**
     * Generate and return payment receipts for a batch of payments
     *
     * @param int $oa_id The order agreement ID
     * @return \Illuminate\Contracts\View\View
     */
    public function showBatchReceipts($oa_id)
    {
        // Get the order
        $order = Order::findOrFail($oa_id);

        // Get all payments for this order, grouped by batch receipt number
        $payments = OrderPaymentHistory::where('oa_id', $oa_id)
            ->orderBy('batch_receipt_number')
            ->orderBy('receipt_sequence')
            ->get();

        // Group payments by batch receipt number
        $batches = [];
        foreach ($payments as $payment) {
            $batchKey = $payment->batch_receipt_number ?? 'single_' . $payment->id;
            if (!isset($batches[$batchKey])) {
                $batches[$batchKey] = [
                    'batch_number' => $payment->batch_receipt_number,
                    'payments' => [],
                    'total' => 0,
                    'date' => $payment->date_of_payment,
                ];
            }
            $batches[$batchKey]['payments'][] = $payment;
            $batches[$batchKey]['total'] += $payment->amount;
        }

        return view('receipts.batch-payments', compact('order', 'batches'));
    }

    /**
     * Generate PDF for a payment receipt
     *
     * @param int $payment_id The payment ID
     * @return \Illuminate\Http\Response
     */
    public function generatePdf($payment_id)
    {
        $payment = OrderPaymentHistory::with('details')->findOrFail($payment_id);

        // If this payment is part of a batch, redirect to the batch receipt print
        if ($payment->isPartOfBatch()) {
            return redirect()->route('receipt.print.batch', [
                'batch_number' => $payment->batch_receipt_number
            ]);
        }

        // For now, we'll just return the view that can be printed from browser
        return view('receipts.payment-pdf', compact('payment'));
    }

    /**
     * Print a consolidated receipt for a batch
     *
     * @param string $batch_number The batch receipt number
     * @return \Illuminate\Http\Response
     */
    public function printBatchReceipt($batch_number)
    {
        $batch_payments = OrderPaymentHistory::where('batch_receipt_number', $batch_number)
            ->orderBy('receipt_sequence')
            ->get();

        if ($batch_payments->isEmpty()) {
            abort(404, 'Batch receipt not found');
        }

        $first_payment = $batch_payments->first();
        $order = $first_payment->details;
        $batch_total = $batch_payments->sum('amount');

        // Get all payment modes used in this batch
        $payment_modes = $batch_payments->pluck('mop')->unique()->toArray();

        return view('receipts.batch-pdf', compact(
            'batch_payments',
            'order',
            'batch_number',
            'batch_total',
            'payment_modes'
        ));
    }

    /**
     * Print all receipts for an order
     *
     * @param int $oa_id The order agreement ID
     * @return \Illuminate\Http\Response
     */
    public function printBatchReceipts($oa_id)
    {
        $order = Order::findOrFail($oa_id);

        // Get all payments for this order, grouped by batch receipt number
        $payments = OrderPaymentHistory::where('oa_id', $oa_id)
            ->orderBy('batch_receipt_number')
            ->orderBy('receipt_sequence')
            ->get();

        // Group payments by batch receipt number
        $batches = [];
        foreach ($payments as $payment) {
            $batchKey = $payment->batch_receipt_number ?? 'single_' . $payment->id;
            if (!isset($batches[$batchKey])) {
                $batches[$batchKey] = [
                    'batch_number' => $payment->batch_receipt_number,
                    'payments' => [],
                    'total' => 0,
                    'date' => $payment->date_of_payment,
                ];
            }
            $batches[$batchKey]['payments'][] = $payment;
            $batches[$batchKey]['total'] += $payment->amount;
        }

        return view('receipts.batch-payments-print', compact('order', 'batches'));
    }

    /**
     * Void a payment receipt
     *
     * @param int $payment_id The payment ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function voidReceipt($payment_id)
    {
        $payment = OrderPaymentHistory::findOrFail($payment_id);

        // Check if this payment can be voided (e.g., not already voided)
        if ($payment->status === 'Voided') {
            return back()->with('error', 'This payment has already been voided.');
        }

        // Void the payment
        $payment->status = 'Voided';
        $payment->save();

        // If it's part of a batch, check if all items in the batch are now voided
        if ($payment->batch_receipt_number) {
            $batch_payments = OrderPaymentHistory::where('batch_receipt_number', $payment->batch_receipt_number)
                ->where('status', '!=', 'Voided')
                ->count();

            if ($batch_payments === 0) {
                // All payments in this batch are now voided
                return redirect()->route('receipt.batch', ['oa_id' => $payment->oa_id])
                    ->with('success', 'The entire batch receipt has been voided.');
            }
        }

        return back()->with('success', 'Payment receipt has been voided successfully.');
    }

    /**
     * Print all payments report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printAllPayments(Request $request)
    {
        // Get filter parameters
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $status = $request->input('status');
        $paymentMode = $request->input('payment_mode');
        $deliveryId = $request->input('delivery_id'); // Get delivery_id
        $search = $request->input('search');

        // Build query with filters
        $query = OrderPaymentHistory::with(['details', 'delivery'])
            ->whereBetween('date_of_payment', [
                $dateFrom,
                Carbon::parse($dateTo)->endOfDay()->toDateTimeString()
            ]);

        // Apply filters
        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($paymentMode)) {
            $query->where('mop', $paymentMode);
        }

        // Apply delivery filter
        if (!empty($deliveryId)) {
            $query->where('delivery_id', $deliveryId);
        }

        // Apply search filter
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $query->where(function($q) use ($searchTerm) {
                $q->where('receipt_number', 'LIKE', $searchTerm)
                ->orWhere('batch_receipt_number', 'LIKE', $searchTerm)
                ->orWhere('reference_no', 'LIKE', $searchTerm)
                ->orWhereHas('details', function($orderQuery) use ($searchTerm) {
                    $orderQuery->where('oa_number', 'LIKE', $searchTerm)
                        ->orWhere('oa_client', 'LIKE', $searchTerm);
                })
                ->orWhereHas('delivery', function($deliveryQuery) use ($searchTerm) {
                    $deliveryQuery->where('transno', 'LIKE', $searchTerm);
                });
            });
        }

        // Get payments and calculate totals
        $payments = $query->orderBy('date_of_payment', 'desc')->get();

        // Calculate totals for different statuses
        $totalPosted = $payments->where('status', 'Posted')->sum('amount');
        $totalUnposted = $payments->where('status', 'Unposted')->sum('amount');
        $totalOnHold = $payments->where('status', 'On-hold')->sum('amount');
        $totalVoided = $payments->where('status', 'Voided')->sum('amount');

        // Return view with data
        return view('payments.print', [
            'payments' => $payments,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'status' => $status,
            'payment_mode' => $paymentMode,
            'delivery_id' => $deliveryId,
            'search' => $search,
            'total_posted' => $totalPosted,
            'total_unposted' => $totalUnposted,
            'total_on_hold' => $totalOnHold,
            'total_voided' => $totalVoided
        ]);
    }
}