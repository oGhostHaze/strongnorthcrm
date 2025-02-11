<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span class="text-uppercase fw-bolder">Order Payments Report</span>
                        <div class="d-flex">
                            <button class="btn btn-sm btn-primary" onClick="print_div()"><i
                                    class="fas fa-print me-1"></i>Print</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 my-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" id="search">FROM</span>
                                <input type="date" class="form-control" aria-label="Search" aria-describedby="search"
                                    wire:model.lazy="start_date">
                                <span class="input-group-text" id="search">TO</span>
                                <input type="date" class="form-control" aria-label="Search" aria-describedby="search"
                                    wire:model.lazy="end_date">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7 my-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" id="search"><i
                                        class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                                    aria-describedby="search" wire:model.lazy="search">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" id='print_div'>
                        <table class="table table-sm table-hover table-striped table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>OA #</th>
                                    <th>Lifechanger</th>
                                    <th>Client</th>
                                    <th>Payment Date</th>
                                    <th>Mode of Payment</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Reference #</th>
                                    <th>Amount due</th>
                                    <th>Recon Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reports as $row)
                                    <tr style="cursor: pointer"
                                        onclick="update_payment('{{ $row->id }}', '{{ $row->status }}')">
                                        <td>{{ $row->details->oa_number }}</td>
                                        <td>
                                            @if ($row->details->oa_consultant and $row->details->oa_consultant != 'N/A' and $row->details->oa_consultant != 'NA')
                                                {{ $row->details->oa_consultant }}
                                            @elseif($row->details->oa_associate and $row->details->oa_associate != 'N/A' and $row->details->oa_associate != 'NA')
                                                {{ $row->details->oa_associate }}
                                            @elseif($row->details->oa_distributor and $row->details->oa_distributor != 'N/A' and $row->details->oa_distributor != 'NA')
                                                {{ $row->details->oa_distributor }}
                                            @endif
                                        </td>
                                        <td>{{ $row->details->oa_client }}</td>
                                        <td>{{ date('F j, Y', strtotime($row->date_of_payment)) }}</td>
                                        <td>{{ $row->mop }}</td>
                                        <td>{{ number_format($row->amount, 2) }}</td>
                                        <td>{{ $row->updated_at }}</td>
                                        <td>{{ $row->reference_no }}</td>
                                        <td>{{ $row->amount }}</td>
                                        <td>{{ $row->recon_date }}</td>
                                        <td>
                                            <span
                                                class="badge text-small @if ($row->status == 'Unposted') bg-secondary @elseif($row->status == 'Posted') bg-primary @elseif($row->status == 'Commissioned') bg-success @elseif($row->status == 'On Hold') bg-danger @endif">{{ $row->status }}</span>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No Record Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Update Payment --}}
    <div class="modal fade" id="updatePaymentModal" tabindex="-1" aria-labelledby="updatePaymentModal"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePaymentModal">Update Payment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" wire:model="status">
                            <option value="Unposted">Unposted</option>
                            <option value="Posted">Posted</option>
                            <option value="Commissioned">Commissioned</option>
                            <option value="On Hold">On Hold</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click="update_payment()" data-bs-dismiss="modal"
                        aria-label="Close">Submit</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Update Payment --}}
</div>

@push('scripts')
    <script>
        function update_payment(id, status) {
            @this.set('payment_id', id);
            @this.set('status', status);

            $('#updatePaymentModal').modal('toggle');
        }

        function print_div() {
            var printContents = document.getElementById('print_div').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(true);
        }
    </script>
@endpush
