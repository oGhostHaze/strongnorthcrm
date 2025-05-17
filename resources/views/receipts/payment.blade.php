@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">PAYMENT RECEIPT</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h4>STRONGNORTH ENTERPRISES OPC</h4>
                            <p>Unit 9 & 10 VYV Bldg., Brgy. 1, San Nicolas, Ilocos Norte</p>
                            <p>VAT: 666-167-922-000</p>
                            <h5 class="mt-4">PAYMENT RECEIPT</h5>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Sold to:</strong> {{ $payment->details->oa_client }}
                                </div>
                                <div class="mb-2">
                                    <strong>Address:</strong> {{ $payment->details->oa_address }}
                                </div>
                                <div class="mb-2">
                                    <strong>Buss. Style:</strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>PR NO:</strong>
                                    {{ $payment->receipt_number ?? 'PR-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="mb-2">
                                    <strong>Date:</strong> {{ date('Y-m-d') }}
                                </div>
                                <div class="mb-2">
                                    <strong>TIN:</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between border p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="cashCheck"
                                            {{ $payment->mop == 'CASH' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cashCheck">cash</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="checkCheck"
                                            {{ $payment->mop == 'CHECK' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="checkCheck">check</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="onlineCheck"
                                            {{ $payment->mop == 'ONLINE' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="onlineCheck">online</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="ccCheck"
                                            {{ $payment->mop == 'CC' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ccCheck">cc</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="partialCheck"
                                            {{ $payment->details->percentage() < 100 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="partialCheck">partial</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="fullCheck"
                                            {{ $payment->details->percentage() >= 100 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fullCheck">full</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>MOP</th>
                                        <th>DATE ISSUED</th>
                                        <th>CHECK/REFERENCE NO.</th>
                                        <th>ORDER NO.</th>
                                        <th>AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $payment->mop }}</td>
                                        <td>{{ date('Y-m-d', strtotime($payment->date_of_payment)) }}</td>
                                        <td>{{ $payment->reference_no ?? '-' }}</td>
                                        <td>{{ $payment->reference_no ?? $payment->details->oa_number }}</td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    @for ($i = 0; $i < 20; $i++)
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    @endfor
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>TOTAL AMOUNT</strong></td>
                                        <td><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-6">
                                <div class="border-top pt-2">
                                    <p class="mb-0">Cashier/Authorized Representative</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-4 text-center border">
                                <p class="mb-0">CLIENT'S COPY</p>
                            </div>
                            <div class="col-md-4 text-center border">
                                <p class="mb-0">CASHIER'S COPY</p>
                            </div>
                            <div class="col-md-4 text-center border">
                                <p class="mb-0">ACCOUNTING COPY</p>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('receipt.print', ['payment_id' => $payment->id]) }}" class="btn btn-primary"
                                target="_blank">Print Receipt</a>
                            <a href="{{ route('order.agreements.view', ['oa' => $payment->details]) }}"
                                class="btn btn-secondary">Back to Order</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
