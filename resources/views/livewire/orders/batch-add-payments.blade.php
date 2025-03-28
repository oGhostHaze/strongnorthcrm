<div>
    <div class="card">
        <div class="card-header">
            <h5>Batch Add Payments</h5>
        </div>
        <div class="card-body">
            <!-- Grid column -->
            <div class="row">
                <div class="text-center col-sm-12">
                    <h3 class="font-weight-bold">ORDER AGREEMENT</h3>
                    <h7 class="font-weight-bold text-danger">{{ $oa->oa_number }}</h7>
                </div>
                <div class="col-sm-12 text-end">
                    <div class="alert alert-info">
                        <strong>Batch Receipt #:</strong> {{ $batchReceiptNumber }}
                        @if ($delivery)
                            <div class="mt-2">
                                <strong>Associated Delivery:</strong> {{ $delivery->transno }}
                                <span class="text-muted">({{ $delivery->date }})</span>
                            </div>
                        @endif
                    </div>
                </div>
                <hr>
            </div>
            <div class="row">
                <!-- Grid column -->
                <div class="text-left col-md-6 border-right">
                    <p class="p-0 m-0"><strong>Date: </strong>{{ $oa->oa_date }}</p>
                    <p class="p-0 m-0"><strong>Client: </strong>{{ $oa->oa_client }}</p>
                    <p class="p-0 m-0"><strong>Address: </strong>{{ $oa->oa_address }}</p>
                    <p class="p-0 m-0"><strong>Contact #: </strong>{{ $oa->oa_contact }}</p>
                </div>
                <!-- Grid column -->
                <!-- Grid column -->
                <div class="text-left col-md-6">
                    <p class="p-0 m-0"><strong>Consultant: </strong>{{ $oa->oa_consultant }}</p>
                    <p class="p-0 m-0"><strong>Associate: </strong>{{ $oa->oa_associate }}</p>
                    <p class="p-0 m-0"><strong>Presenter: </strong>{{ $oa->oa_presenter }}</p>
                    <p class="p-0 m-0"><strong>Team Builder: </strong>{{ $oa->oa_team_builder }}</p>
                    <p class="p-0 m-0"><strong>Distributor: </strong>{{ $oa->oa_distributor }}</p>
                </div>
                <!-- Grid column -->
            </div>
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Item #</th>
                            <th>Mode of Payment</th>
                            <th>Amount</th>
                            <th>Date of Payment</th>
                            <th>Due Date</th>
                            <th>PDC Date</th>
                            <th>Reference No.</th>
                            <th>Reconciliation Date</th>
                            <th>Remarks</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $index => $payment)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <select class="form-select" wire:model="payments.{{ $index }}.mop">
                                        <option value="">Select</option>
                                        @foreach ($modes_of_payment as $mop)
                                            <option value="{{ $mop->legend }}">{{ $mop->legend }}</option>
                                        @endforeach
                                    </select>
                                    @error("payments.$index.mop")
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" class="form-control"
                                        wire:model="payments.{{ $index }}.amount">
                                    @error("payments.$index.amount")
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <input type="date" class="form-control"
                                        wire:model="payments.{{ $index }}.date_of_payment">
                                    @error("payments.$index.date_of_payment")
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <input type="date" class="form-control"
                                        wire:model="payments.{{ $index }}.due_date">
                                    @error("payments.$index.due_date")
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <input type="date" class="form-control"
                                        wire:model="payments.{{ $index }}.pdc_date">
                                    @error("payments.$index.pdc_date")
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control"
                                        wire:model="payments.{{ $index }}.reference_no">
                                    @error("payments.$index.reference_no")
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <input type="date" class="form-control"
                                        wire:model="payments.{{ $index }}.recon_date">
                                    @error("payments.$index.recon_date")
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control"
                                        wire:model="payments.{{ $index }}.remarks">
                                    @error("payments.$index.remarks")
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <select class="form-select" wire:model="payments.{{ $index }}.status">
                                        <option value="Unposted">Unposted</option>
                                        <option value="Posted">Posted</option>
                                        <option value="On-hold">On-hold</option>
                                    </select>
                                    @error("payments.$index.status")
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm"
                                        wire:click="removePaymentRow({{ $index }})">
                                        ❌
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex">
                <button class="btn btn-primary me-2" wire:click="addPaymentRow">➕ Add Row</button>
                <button class="btn btn-success" wire:click="savePayments">💾 Save Batch Payment</button>
            </div>
        </div>
    </div>
</div>
