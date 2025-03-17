@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Payment Receipts for Order #{{ $order->oa_number }}</h4>
                        <a href="{{ route('receipt.batch.print', ['oa_id' => $order->oa_id]) }}" class="btn btn-light"
                            target="_blank">
                            <i class="fa-solid fa-print"></i> Print All Receipts
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Order Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Client:</strong> {{ $order->oa_client }}</p>
                                    <p><strong>Address:</strong> {{ $order->oa_address }}</p>
                                    <p><strong>Contact:</strong> {{ $order->oa_contact }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Order Date:</strong> {{ date('F d, Y', strtotime($order->oa_date)) }}</p>
                                    <p><strong>Order Total:</strong>
                                        ₱{{ number_format($order->oa_price_override ?? $order->items->sum('item_total') + $order->oa_price_diff, 2) }}
                                    </p>
                                    <p><strong>Payment Status:</strong>
                                        <span class="badge {{ $order->percentage() >= 100 ? 'bg-success' : 'bg-warning' }}">
                                            {{ $order->percentage() }}% Paid
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <h5>Payment Batches</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Date</th>
                                        <th>Payment Items</th>
                                        <th>Total Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($batches as $batchKey => $batch)
                                        <tr>
                                            <td>{{ $batch['batch_number'] ?? 'Individual Payment' }}</td>
                                            <td>{{ date('M d, Y', strtotime($batch['date'])) }}</td>
                                            <td>{{ count($batch['payments']) }}</td>
                                            <td>₱{{ number_format($batch['total'], 2) }}</td>
                                            <td>
                                                @if ($batch['batch_number'])
                                                    <a href="{{ route('receipt.show.batch', ['batch_number' => $batch['batch_number']]) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fa-solid fa-receipt"></i> View Receipt
                                                    </a>
                                                    <a href="{{ route('receipt.print.batch', ['batch_number' => $batch['batch_number']]) }}"
                                                        class="btn btn-sm btn-secondary" target="_blank">
                                                        <i class="fa-solid fa-print"></i> Print
                                                    </a>
                                                @else
                                                    <a href="{{ route('receipt.show', ['payment_id' => $batch['payments'][0]->id]) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fa-solid fa-receipt"></i> View Receipt
                                                    </a>
                                                    <a href="{{ route('receipt.print', ['payment_id' => $batch['payments'][0]->id]) }}"
                                                        class="btn btn-sm btn-secondary" target="_blank">
                                                        <i class="fa-solid fa-print"></i> Print
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No payment records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total Payments:</strong></td>
                                        <td><strong>₱{{ number_format(collect($batches)->sum('total'), 2) }}</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('order.agreements.view', ['oa' => $order]) }}" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left"></i> Back to Order
                            </a>
                            <a href="{{ route('order.agreements.batch-add-payments', ['oa_id' => $order->oa_id]) }}"
                                class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i> Add More Payments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
