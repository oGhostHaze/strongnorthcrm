<div class="container-fluid">
    <div class="row d-flex justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header text-uppercase fw-bold">
                    <div class="d-flex justify-content-between">
                        <span>Return Slip for Order #{{ $rsn->oa_no }}</span>
                        <div class="d-flex">
                            @if ($rsn->status == 'For Approval')
                                @can('manage-merchandise-orders')
                                    <a href="" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                        data-bs-target="#returnItemModal">Return Item</a>
                                @endcan
                                @can('approve-return-order')
                                    <a href="#" class="btn btn-sm btn-secondary ms-2" wire:click="confirm_approval()"
                                        wire:loading.attr='disabled'>Approve Returns</a>
                                @endcan
                            @else
                                <a href="#" class="btn btn-sm btn-info ms-2" wire:click="print_this()"
                                    {{-- onclick="printdiv('print_div')" --}}>Print</a>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="card-body" id='print_div'>
                    <div id="page1">
                        <!-- Grid column -->
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h3 class="font-weight-bold">MERCHANDISE RETURN SLIP</h3>
                                <h7 class="font-weight-bold text-danger">MRSN-{{ $rsn->id }}</h7>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Grid column -->
                            <div class="col-md-6 text-left border-right">
                                <p class="p-0 m-0"><strong>Date: </strong>{{ $rsn->created_at }}</p>
                                <p class="p-0 m-0"><strong>Client: </strong>{{ $rsn->oa->client }}</p>
                                <p class="p-0 m-0"><strong>Address: </strong>{{ $rsn->oa->address }}</p>
                                <p class="p-0 m-0"><strong>Contact #: </strong>{{ $rsn->oa->contact }}</p>
                            </div>
                            <!-- Grid column -->
                            <!-- Grid column -->
                            <div class="col-md-6 text-left">
                                <p class="p-0 m-0"><strong>Consultant: </strong>{{ $rsn->oa->consultant }}</p>
                                <p class="p-0 m-0"><strong>Associate: </strong>{{ $rsn->oa->associate }}</p>
                                <p class="p-0 m-0"><strong>OA #: </strong>{{ $rsn->oa_no }}</p>
                            </div>
                            <!-- Grid column -->
                        </div>
                        <div class="row">
                            <table class="table table-sm table-hover table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Product Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rsn->return_items()->get() as $item)
                                        <tr>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td class="text-center">{{ $item->item->item }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-muted" colspan="7">No items found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr class="low">
                                        <th class="text-center font-weight-bold">INSPECTED BY:</th>
                                        <th class="text-center font-weight-bold">RECEIVED FROM:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center"></br></br>
                                            <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature over
                                                printed name</span>
                                        </td>
                                        <td class="text-center"></br></br>
                                            <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature over
                                                printed name</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <span class="py-0 my-0 font-small font-weight-bolder text-danger">-- Office
                                                Copy --</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($print_val)
                        <div id="page2">
                            <!-- Grid column -->
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <h3 class="font-weight-bold">MERCHANDISE RETURN SLIP</h3>
                                    <h7 class="font-weight-bold text-danger">MRSN-{{ $rsn->id }}</h7>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Grid column -->
                                <div class="col-md-6 text-left border-right">
                                    <p class="p-0 m-0"><strong>Date: </strong>{{ $rsn->created_at }}</p>
                                    <p class="p-0 m-0"><strong>Client: </strong>{{ $rsn->oa->client }}</p>
                                    <p class="p-0 m-0"><strong>Address: </strong>{{ $rsn->oa->address }}</p>
                                    <p class="p-0 m-0"><strong>Contact #: </strong>{{ $rsn->oa->contact }}</p>
                                </div>
                                <!-- Grid column -->
                                <!-- Grid column -->
                                <div class="col-md-6 text-left">
                                    <p class="p-0 m-0"><strong>Consultant: </strong>{{ $rsn->oa->consultant }}</p>
                                    <p class="p-0 m-0"><strong>Associate: </strong>{{ $rsn->oa->associate }}</p>
                                    <p class="p-0 m-0"><strong>OA #: </strong>{{ $rsn->oa_no }}</p>
                                </div>
                                <!-- Grid column -->
                            </div>
                            <div class="row">
                                <table class="table table-sm table-hover table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Product Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($rsn->return_items()->get() as $item)
                                            <tr>
                                                <td class="text-center">{{ $item->qty }}</td>
                                                <td class="text-center">{{ $item->item->item }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center text-muted" colspan="7">No items found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr class="low">
                                            <th class="text-center font-weight-bold">INSPECTED BY:</th>
                                            <th class="text-center font-weight-bold">RECEIVED FROM:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"></br></br>
                                                <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature over
                                                    printed name</span>
                                            </td>
                                            <td class="text-center"></br></br>
                                                <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature over
                                                    printed name</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <span class="py-0 my-0 font-small font-weight-bolder text-danger">--
                                                    Releasing Copy --</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="page3">
                            <!-- Grid column -->
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <h3 class="font-weight-bold">MERCHANDISE RETURN SLIP</h3>
                                    <h7 class="font-weight-bold text-danger">MRSN-{{ $rsn->id }}</h7>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Grid column -->
                                <div class="col-md-6 text-left border-right">
                                    <p class="p-0 m-0"><strong>Date: </strong>{{ $rsn->created_at }}</p>
                                    <p class="p-0 m-0"><strong>Client: </strong>{{ $rsn->oa->client }}</p>
                                    <p class="p-0 m-0"><strong>Address: </strong>{{ $rsn->oa->address }}</p>
                                    <p class="p-0 m-0"><strong>Contact #: </strong>{{ $rsn->oa->contact }}</p>
                                </div>
                                <!-- Grid column -->
                                <!-- Grid column -->
                                <div class="col-md-6 text-left">
                                    <p class="p-0 m-0"><strong>Consultant: </strong>{{ $rsn->oa->consultant }}</p>
                                    <p class="p-0 m-0"><strong>Associate: </strong>{{ $rsn->oa->associate }}</p>
                                    <p class="p-0 m-0"><strong>OA #: </strong>{{ $rsn->oa_no }}</p>
                                </div>
                                <!-- Grid column -->
                            </div>
                            <div class="row">
                                <table class="table table-sm table-hover table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Product Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($rsn->return_items()->get() as $item)
                                            <tr>
                                                <td class="text-center">{{ $item->qty }}</td>
                                                <td class="text-center">{{ $item->item->item }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center text-muted" colspan="7">No items found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr class="low">
                                            <th class="text-center font-weight-bold">INSPECTED BY:</th>
                                            <th class="text-center font-weight-bold">RECEIVED FROM:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"></br></br>
                                                <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                    over printed name</span>
                                            </td>
                                            <td class="text-center"></br></br>
                                                <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                    over printed name</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <span class="py-0 my-0 font-small font-weight-bolder text-danger">--
                                                    Admin Copy --</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="page4">
                            <!-- Grid column -->
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <h3 class="font-weight-bold">MERCHANDISE RETURN SLIP</h3>
                                    <h7 class="font-weight-bold text-danger">MRSN-{{ $rsn->id }}</h7>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Grid column -->
                                <div class="col-md-6 text-left border-right">
                                    <p class="p-0 m-0"><strong>Date: </strong>{{ $rsn->created_at }}</p>
                                    <p class="p-0 m-0"><strong>Client: </strong>{{ $rsn->oa->client }}</p>
                                    <p class="p-0 m-0"><strong>Address: </strong>{{ $rsn->oa->address }}</p>
                                    <p class="p-0 m-0"><strong>Contact #: </strong>{{ $rsn->oa->contact }}</p>
                                </div>
                                <!-- Grid column -->
                                <!-- Grid column -->
                                <div class="col-md-6 text-left">
                                    <p class="p-0 m-0"><strong>Consultant: </strong>{{ $rsn->oa->consultant }}</p>
                                    <p class="p-0 m-0"><strong>Associate: </strong>{{ $rsn->oa->associate }}</p>
                                    <p class="p-0 m-0"><strong>OA #: </strong>{{ $rsn->oa_no }}</p>
                                </div>
                                <!-- Grid column -->
                            </div>
                            <div class="row">
                                <table class="table table-sm table-hover table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Product Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($rsn->return_items()->get() as $item)
                                            <tr>
                                                <td class="text-center">{{ $item->qty }}</td>
                                                <td class="text-center">{{ $item->item->item }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center text-muted" colspan="7">No items found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr class="low">
                                            <th class="text-center font-weight-bold">INSPECTED BY:</th>
                                            <th class="text-center font-weight-bold">RECEIVED FROM:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"></br></br>
                                                <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                    over printed name</span>
                                            </td>
                                            <td class="text-center"></br></br>
                                                <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                    over printed name</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <span class="py-0 my-0 font-small font-weight-bolder text-danger">--
                                                    Client Copy --</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Add Item Modal --}}
    <div class="modal fade" id="returnItemModal" tabindex="-1" aria-labelledby="returnItemModal"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnItemModalLabel">Return Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="item_id" class="form-label">Item</label><br>
                        <select class="form-control @error('item_id') is-invalid @enderror" id="item_id"
                            wire:model.defer="item_id" style="width: 100%;">
                            <option value=""></option>
                            @forelse ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->item->item }} | Qty:
                                    {{ $item->item_qty_released }}</option>
                            @empty
                            @endforelse
                        </select>
                        @error('item_id')
                            <span class="text-danger text-small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="item_qty"
                            class="form-label @error('item_qty') is-invalid @enderror">Quantity</label>
                        <input type="number" class="form-control" id="item_qty" step="1"
                            wire:model.defer="item_qty">
                        @error('item_qty')
                            <span class="text-danger text-small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="item_remarks" class="form-label">Remarks</label>
                        <input type="text" class="form-control" id="item_remarks"
                            wire:model.defer="item_remarks">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="return_item('item')">Submit</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Add Item Modal --}}
</div>

@push('scripts')
    <script>
        window.addEventListener('print_div', event => {
            var printContents = document.getElementById('print_div').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(true);
        });
    </script>
@endpush
