<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header text-uppercase fw-bold">
                    <div class="d-flex justify-content-between">
                        <span>View Servicing #{{$servicing->sr_no}}</span>
                        <div class="d-flex">
                            <a href="" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#overrideTotalModal">Override Total Price</a>
                            <a href="" class="btn btn-sm btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#priceDifferenceModal">Price Difference</a>
                            <a href="#" class="btn btn-sm btn-info ms-2" wire:click="print_this()" {{-- onclick="printdiv('print_div')" --}}>Print</a>
                        </div>
                    </div>

                </div>
                <div class="card-body" id="print_div">
                        <!-- Grid column -->
                        <div class="row mb-1">
                            <div class="col-3 ps-4 mb-0">
                                <img src="{{url('img/str.png')}}" alt="" class="img-fluid" style="max-width: 60%">
                            </div>
                            <div class="col-6 text-center">
                                <h3 class="fw-bold my-0">StrongNorth Cookware Trading</h3>
                                <p class="small p-0 my-0">(Independent Authorized Dealer)</p>
                                <p class="small p-0 my-0">9-10 VYV Bldg., Valdez Center, Brgy 1 San Nicolas, Ilocos Norte</p>
                                <p class="small p-0 my-0">Contact: 0917-891-9180</p>
                            </div>
                            <div class="col-3 pe-4 mb-0 text-end">
                                <img src="{{url('img/right.png')}}" alt="" class="img-fluid" style="max-width: 60%">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                    <h3 class="fw-bold my-0 py-0">SERVICING RECEIPT</h3>
                                    <hr class="my-1">
                            </div>
                        </div>
                        <div class="row">
                            <!-- Grid column -->
                            <div class="col-6 text-left border-right">
                                <p class="p-0 m-0"><strong>Client: </strong>{{$servicing->client}}</p>
                                <p class="p-0 m-0"><strong>Contact #: </strong>{{$servicing->contact}}</p>
                                <p class="p-0 m-0"><strong>Received from: </strong>{{$servicing->received_from}}</p>
                                <p class="p-0 m-0"><strong>Inspected by: </strong>{{$servicing->inspected_by}}</p>
                            </div>
                            <!-- Grid column -->
                            <!-- Grid column -->
                            <div class="col-6 text-left">
                                <p class="p-0 m-0"><strong>SR #: </strong>{{$servicing->sr_no}}</p>
                                <p class="p-0 m-0"><strong>Date Received: </strong>{{$servicing->date_received}}</p>
                            </div>
                            <!-- Grid column -->
                        </div>
                        <div class="row">
                            <font size="2" class="mt-0">
                                <table class="table table-sm table-hover table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Parts Included</th>
                                            <th>Description</th>
                                            <th>Action Needed</th>
                                            <th class="text-end">Price</th>
                                            <th>Model</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($servicing->items()->get() as $item)
                                            <tr>
                                                <td>{{$item->product->product_description}}</td>
                                                <td>{{$item->parts_included}}</td>
                                                <td>{{$item->item_qty}}</td>
                                                <td>{{$item->action_needed}}</td>
                                                <td class="text-end">{{number_format($item->price,2)}}</td>
                                                <td>{{$item->model}}</td>
                                            </tr>
                                        @empty

                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class='table-light'><td class="text-end" colspan='4'><strong>TOTAL:</strong></td> <td class="text-end"><strong>&#8369; {{number_format((float)$servicing->items()->sum('price'),2)}}</strong></td><td></td></tr>
                                    </tfoot>
                                </table>
                                <table class="table table-bordered table-sm m-0">
                                    <tbody>
                                        <tr class="low">
                                            <th class="text-center fw-bold"></th>
                                            <th class="text-center fw-bold"></th>
                                            <th class="text-center fw-bold"></th>
                                            <th class="text-center fw-bold"></th>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><div class="text-center fw-bold">RECEIVED BY:</div></br></br><div><hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature over printed name</span></div></td>
                                            <td class="w-25"></td>
                                            <td class="w-25"></td>
                                            <td class="w-25"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <span class="py-0 my-0 font-small fw-bolder text-danger">-- Office Copy --</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </font>
                        </div>
                        <br>
                        <hr>
                    @if($print_val)
                        <!-- Grid column -->
                        <div class="row mb-1">
                            <div class="col-3 ps-4 mb-0">
                                <img src="{{url('img/str.png')}}" alt="" class="img-fluid" style="max-width: 60%">
                            </div>
                            <div class="col-6 text-center">
                                <h3 class="fw-bold my-0">StrongNorth Cookware Trading</h3>
                                <p class="small p-0 my-0">(Independent Authorized Dealer)</p>
                                <p class="small p-0 my-0">9-10 VYV Bldg., Valdez Center, Brgy 1 San Nicolas, Ilocos Norte</p>
                                <p class="small p-0 my-0">Contact: 0917-891-9180</p>
                            </div>
                            <div class="col-3 pe-4 mb-0 text-end">
                                <img src="{{url('img/right.png')}}" alt="" class="img-fluid" style="max-width: 60%">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                    <h3 class="fw-bold my-0 py-0">SERVICING RECEIPT</h3>
                                    <hr class="my-1">
                            </div>
                        </div>
                        <div class="row">
                            <!-- Grid column -->
                            <div class="col-6 text-left border-right">
                                <p class="p-0 m-0"><strong>Client: </strong>{{$servicing->client}}</p>
                                <p class="p-0 m-0"><strong>Contact #: </strong>{{$servicing->contact}}</p>
                                <p class="p-0 m-0"><strong>Received from: </strong>{{$servicing->received_from}}</p>
                                <p class="p-0 m-0"><strong>Inspected by: </strong>{{$servicing->inspected_by}}</p>
                            </div>
                            <!-- Grid column -->
                            <!-- Grid column -->
                            <div class="col-6 text-left">
                                <p class="p-0 m-0"><strong>SR #: </strong>{{$servicing->sr_no}}</p>
                                <p class="p-0 m-0"><strong>Date Received: </strong>{{$servicing->date_received}}</p>
                            </div>
                            <!-- Grid column -->
                        </div>
                        <div class="row">
                            <font size="2" class="mt-0">
                                <table class="table table-sm table-hover table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Parts Included</th>
                                            <th>Description</th>
                                            <th>Action Needed</th>
                                            <th class="text-end">Price</th>
                                            <th>Model</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($servicing->items()->get() as $item)
                                            <tr>
                                                <td>{{$item->product->product_description}}</td>
                                                <td>{{$item->parts_included}}</td>
                                                <td>{{$item->item_qty}}</td>
                                                <td>{{$item->action_needed}}</td>
                                                <td class="text-end">{{number_format($item->price,2)}}</td>
                                                <td>{{$item->model}}</td>
                                            </tr>
                                        @empty

                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class='table-light'><td class="text-end" colspan='4'><strong>TOTAL:</strong></td> <td class="text-end"><strong>&#8369; {{number_format((float)$servicing->items()->sum('price'),2)}}</strong></td><td></td></tr>
                                    </tfoot>
                                </table>
                                <table class="table table-bordered table-sm m-0">
                                    <thead>
                                        <tr class="low">
                                            <th class="text-center fw-bold">RECEIVED BY:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"></br></br><hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature over printed name</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <span class="py-0 my-0 font-small fw-bolder text-danger">-- Client Copy --</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </font>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

        <div class="modal fade" id="overrideTotalModal" tabindex="-1" aria-labelledby="overrideTotalModal" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="overrideTotalModal">Total Price Override</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <div class="mb-3">
                            <label for="price_override" class="form-label">Total Price Override</label>
                            <input type="number" class="form-control" id="price_override" step="0.01" wire:model.defer="price_override">
                        </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger" wire:click="cancel_override()">Cancel Override</button>
                <button type="button" class="btn btn-primary" wire:click="override_price()">Submit</button>
                </div>
            </div>
            </div>
        </div>



        <div class="modal fade" id="priceDifferenceModal" tabindex="-1" aria-labelledby="priceDifferenceModal" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="priceDifferenceModal">Price Difference</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <div class="mb-3">
                            <label for="price_difference" class="form-label">Price Difference</label>
                            <input type="number" class="form-control" id="price_difference" step="0.01" wire:model.defer="price_difference">
                        </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-primary" wire:click="add_pricediff()">Submit</button>
                </div>
            </div>
            </div>
        </div>
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
