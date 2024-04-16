@php
    $row_count = 25;
@endphp
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header text-uppercase fw-bold">
                    <div class="d-flex justify-content-between">
                        <span>View Order #{{ $delivery->transno }}</span>
                        <div class="d-flex">
                            <a href="" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                data-bs-target="#overrideTotalModal">Override Total Price</a>
                            <a href="" class="btn btn-sm btn-warning ms-2" data-bs-toggle="modal"
                                data-bs-target="#priceDifferenceModal">Price Difference</a>
                            @if (!$delivery->print_count)
                                <button class="btn btn-sm btn-secondary ms-2" wire:click="finalize_dr()"
                                    wire:loading.attr='disabled'>Finalize DR</button>
                                <button class="btn btn-sm btn-danger ms-2" wire:click="cancel_dr()"
                                    wire:loading.attr='disabled'>Cancel DR</button>
                            @endif
                            <a href="#" class="btn btn-sm btn-info ms-2" wire:click="print_this()"
                                {{-- onclick="printdiv('print_div')" --}}>Print</a>
                        </div>
                    </div>

                </div>
                <div class="card-body" id="print_div">
                    <section class="page1">
                        <!-- Grid column -->
                        <div class="row mb-1">
                            <div class="col-3 ps-4 mb-0">
                                <img src="{{ url('img/str.png') }}" alt="" class="img-fluid"
                                    style="max-width: 60%">
                            </div>
                            <div class="col-6 text-center">
                                <h3 class="fw-bold my-0">StrongNorth Cookware Trading</h3>
                                <p class="small p-0 my-0">(Independent Authorized Dealer)</p>
                                <p class="small p-0 my-0">9-10 VYV Bldg., Valdez Center, Brgy 1 San Nicolas, Ilocos
                                    Norte</p>
                                <p class="small p-0 my-0">Contact: 0917-891-9180</p>
                            </div>
                            <div class="col-3 pe-4 mb-0 text-end">
                                <img src="{{ url('img/right.png') }}" alt="" class="img-fluid"
                                    style="max-width: 60%">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h3 class="fw-bold my-0 py-0">DELIVERY RECEIPT</h3>
                                <h5 class="h5 text-danger my-0 py-0">{{ $delivery->transno }}</h5>
                                <hr class="my-1">
                            </div>
                        </div>
                        <div class="row">
                            <!-- Grid column -->
                            <div class="col-6 text-left border-right">
                                <p class="p-0 m-0"><strong>Date: </strong>{{ $delivery->date }}</p>
                                <p class="p-0 m-0"><strong>Client: </strong>{{ $delivery->client }}</p>
                                <p class="p-0 m-0"><strong>Address: </strong>{{ $delivery->address }}</p>
                                <p class="p-0 m-0"><strong>Contact #: </strong>{{ $delivery->contact }}</p>
                                <p class="p-0 m-0"><strong>Code: </strong>{{ $delivery->code }}</p>
                            </div>
                            <!-- Grid column -->
                            <!-- Grid column -->
                            <div class="col-6 text-left">
                                <p class="p-0 m-0"><strong>Consultant: </strong>{{ $delivery->consultant }}</p>
                                <p class="p-0 m-0"><strong>Associate: </strong>{{ $delivery->associate }}</p>
                                <p class="p-0 m-0"><strong>Presenter: </strong>{{ $delivery->presenter }}</p>
                                <p class="p-0 m-0"><strong>Team Builder: </strong>{{ $delivery->teambuilder }}</p>
                                <p class="p-0 m-0"><strong>Distributor: </strong>{{ $delivery->distributor }}</p>
                            </div>
                            <!-- Grid column -->
                        </div>
                        <div class="row">
                            <font size="2" class="mt-0">
                                <table class="table table-sm table-hover table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">Quantity</th>
                                            <th width="50%">Articles</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-end">Amount Due</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($delivery->items()->get() as $order)
                                            @php
                                                $row_count--;
                                            @endphp
                                            <tr @if (!$delivery->print_count) @if ($order->status == 'To Follow') onClick="release('{{ $order->item_qty }}', '{{ $order->item->item }}', '{{ $order->id }}', 1)" @endif
                                                @if ($order->status == 'For Releasing') onClick="to_follow('{{ $order->item_qty }}', '{{ $order->item->item }}', '{{ $order->id }}', 1)" @endif
                                                style="cursor: pointer" @endif>

                                                <td class="text-center">{{ $order->item_qty }}</td>
                                                <td>{{ $order->item->item }} - <span
                                                        class="text-danger text-small">{{ $order->status }}</span></td>
                                                <td class="text-end">{{ number_format($order->item_price, 2) }}</td>
                                                <td class="text-end">{{ number_format($order->item_total, 2) }}</td>
                                            </tr>
                                            @empty
                                                @php
                                                    $row_count--;
                                                @endphp
                                                <tr>
                                                    <td class="text-center text-white" colspan="4">-</td>
                                                </tr>
                                            @endforelse
                                            @for ($row = 0; $row < $row_count; $row++)
                                                <tr>
                                                    <td class="text-center text-white" colspan="4"> -</td>
                                                </tr>
                                            @endfor
                                            <tr class='table-light'>
                                                <td class="text-end" colspan='3'><strong>SUBTOTAL:</strong></td>
                                                <td class='text-end'><span>&#8369;
                                                    </span>{{ number_format($subtotal = $delivery->price_override ? $delivery->price_override : $delivery->items()->sum('item_total'), 2) }}
                                                </td>
                                            </tr>
                                            <tr class='table-light'>
                                                <td class="text-end" colspan='3'><strong>PRICE DIFFERENCE:</strong></td>
                                                <td class='text-end'><span>&#8369;
                                                    </span>{{ number_format($price_diff = $delivery->price_diff, 2) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class='table-light'>
                                                <td class="text-end" colspan='3'><strong>TOTAL:</strong></td>
                                                <td class="text-end"><strong>&#8369;
                                                        {{ number_format((float) $subtotal + (float) $price_diff, 2) }}</strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <table class="table table-bordered table-sm m-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-center" width="25%">
                                                    <p class="text-left"><strong>Delivered by: </strong></p></br></br>
                                                    <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature over
                                                        printed name</span>
                                                </td>
                                                <td class="text-center" width="25%">
                                                    <p class="text-left"><strong>Inspected Complete by: </strong></p>
                                                    </br></br>
                                                    <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                        over printed name</span>
                                                </td>
                                                <td class="text-center" width="25%">
                                                    <p class="text-left"><strong>Date Received: </strong></p></br></br>
                                                    <hr class="py-0 my-0">
                                                </td>
                                                <td class="text-center text-wrap text-center" width="25%">
                                                    <p><strong>Received in good order and condition by: </strong></p>
                                                    </br></br>
                                                    <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Customer
                                                        Signature over printed name</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered table-sm m-0">
                                        <thead>
                                            <tr class="low">
                                                <th class="text-center fw-bold">PREPARED BY:</th>
                                                <th class="text-center fw-bold">RELEASED BY:</th>
                                                <th class="text-center fw-bold">APPROVED BY:</th>
                                                <th class="text-center fw-bold">NOTED BY:</th>
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
                                                    <span class="py-0 my-0 font-small fw-bolder text-danger">-- Office Copy
                                                        --</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </font>
                        </section>
                        @if ($print_val)
                            <section class="page2">
                                <!-- Grid column -->
                                <div class="row mb-1">
                                    <div class="col-3 ps-4 mb-0">
                                        <img src="{{ url('img/str.png') }}" alt="" class="img-fluid"
                                            style="max-width: 60%">
                                    </div>
                                    <div class="col-6 text-center">
                                        <h3 class="fw-bold my-0">StrongNorth Cookware Trading</h3>
                                        <p class="small p-0 my-0">(Independent Authorized Dealer)</p>
                                        <p class="small p-0 my-0">9-10 VYV Bldg., Valdez Center, Brgy 1 San Nicolas, Ilocos
                                            Norte</p>
                                        <p class="small p-0 my-0">Contact: 0917-891-9180</p>
                                    </div>
                                    <div class="col-3 pe-4 mb-0 text-end">
                                        <img src="{{ url('img/right.png') }}" alt="" class="img-fluid"
                                            style="max-width: 60%">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        <h3 class="fw-bold my-0 py-0">DELIVERY RECEIPT</h3>
                                        <h5 class="h5 text-danger my-0 py-0">{{ $delivery->transno }}</h5>
                                        <hr class="my-1">
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Grid column -->
                                    <div class="col-6 text-left border-right">
                                        <p class="p-0 m-0"><strong>Date: </strong>{{ $delivery->date }}</p>
                                        <p class="p-0 m-0"><strong>Client: </strong>{{ $delivery->client }}</p>
                                        <p class="p-0 m-0"><strong>Address: </strong>{{ $delivery->address }}</p>
                                        <p class="p-0 m-0"><strong>Contact #: </strong>{{ $delivery->contact }}</p>
                                        <p class="p-0 m-0"><strong>Code: </strong>{{ $delivery->code }}</p>
                                    </div>
                                    <!-- Grid column -->
                                    <!-- Grid column -->
                                    <div class="col-6 text-left">
                                        <p class="p-0 m-0"><strong>Consultant: </strong>{{ $delivery->consultant }}</p>
                                        <p class="p-0 m-0"><strong>Associate: </strong>{{ $delivery->associate }}</p>
                                        <p class="p-0 m-0"><strong>Presenter: </strong>{{ $delivery->presenter }}</p>
                                        <p class="p-0 m-0"><strong>Team Builder: </strong>{{ $delivery->teambuilder }}</p>
                                        <p class="p-0 m-0"><strong>Distributor: </strong>{{ $delivery->distributor }}</p>
                                    </div>
                                    <!-- Grid column -->
                                </div>
                                <div class="row">
                                    <font size="2" class="mt-0">
                                        <table class="table table-sm table-hover table-bordered mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-center">Quantity</th>
                                                    <th width="50%">Articles</th>
                                                    <th class="text-end">Unit Price</th>
                                                    <th class="text-end">Amount Due</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($delivery->items()->get() as $order)
                                                    <tr>
                                                        <td class="text-center">{{ $order->item_qty }}</td>
                                                        <td>{{ $order->item->item }} - <span
                                                                class="text-danger text-small">{{ $order->status }}</span>
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->item_price, 2) }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->item_total, 2) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4">-</td>
                                                    </tr>
                                                @endforelse
                                                @for ($row = 0; $row < $row_count; $row++)
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4"> -</td>
                                                    </tr>
                                                @endfor
                                                <tr class='table-light'>
                                                    <td class="text-end" colspan='3'><strong>SUBTOTAL:</strong></td>
                                                    <td class='text-end'><span>&#8369;
                                                        </span>{{ number_format($subtotal = $delivery->price_override ? $delivery->price_override : $delivery->items()->sum('item_total'), 2) }}
                                                    </td>
                                                </tr>
                                                <tr class='table-light'>
                                                    <td class="text-end" colspan='3'><strong>PRICE DIFFERENCE:</strong>
                                                    </td>
                                                    <td class='text-end'><span>&#8369;
                                                        </span>{{ number_format($price_diff = $delivery->price_diff, 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class='table-light'>
                                                    <td class="text-end" colspan='3'><strong>TOTAL:</strong></td>
                                                    <td class="text-end"><strong>&#8369;
                                                            {{ number_format((float) $subtotal + (float) $price_diff, 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <table class="table table-bordered table-sm m-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center" width="25%">
                                                        <p class="text-left"><strong>Delivered by: </strong></p></br></br>
                                                        <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                            over printed name</span>
                                                    </td>
                                                    <td class="text-center" width="25%">
                                                        <p class="text-left"><strong>Inspected Complete by: </strong></p>
                                                        </br></br>
                                                        <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                            over printed name</span>
                                                    </td>
                                                    <td class="text-center" width="25%">
                                                        <p class="text-left"><strong>Date Received: </strong></p></br></br>
                                                        <hr class="py-0 my-0">
                                                    </td>
                                                    <td class="text-center text-wrap text-center" width="25%">
                                                        <p><strong>Received in good order and condition by: </strong></p>
                                                        </br></br>
                                                        <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Customer
                                                            Signature over printed name</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered table-sm m-0">
                                            <thead>
                                                <tr class="low">
                                                    <th class="text-center fw-bold">PREPARED BY:</th>
                                                    <th class="text-center fw-bold">RELEASED BY:</th>
                                                    <th class="text-center fw-bold">APPROVED BY:</th>
                                                    <th class="text-center fw-bold">NOTED BY:</th>
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
                                                        <span class="py-0 my-0 font-small fw-bolder text-danger">-- Admin
                                                            Copy --</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </font>
                            </section>
                            <section class="page3">
                                <!-- Grid column -->
                                <div class="row mb-1">
                                    <div class="col-3 ps-4 mb-0">
                                        <img src="{{ url('img/str.png') }}" alt="" class="img-fluid"
                                            style="max-width: 60%">
                                    </div>
                                    <div class="col-6 text-center">
                                        <h3 class="fw-bold my-0">StrongNorth Cookware Trading</h3>
                                        <p class="small p-0 my-0">(Independent Authorized Dealer)</p>
                                        <p class="small p-0 my-0">9-10 VYV Bldg., Valdez Center, Brgy 1 San Nicolas, Ilocos
                                            Norte</p>
                                        <p class="small p-0 my-0">Contact: 0917-891-9180</p>
                                    </div>
                                    <div class="col-3 pe-4 mb-0 text-end">
                                        <img src="{{ url('img/right.png') }}" alt="" class="img-fluid"
                                            style="max-width: 60%">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        <h3 class="fw-bold my-0 py-0">DELIVERY RECEIPT</h3>
                                        <h5 class="h5 text-danger my-0 py-0">{{ $delivery->transno }}</h5>
                                        <hr class="my-1">
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Grid column -->
                                    <div class="col-6 text-left border-right">
                                        <p class="p-0 m-0"><strong>Date: </strong>{{ $delivery->date }}</p>
                                        <p class="p-0 m-0"><strong>Client: </strong>{{ $delivery->client }}</p>
                                        <p class="p-0 m-0"><strong>Address: </strong>{{ $delivery->address }}</p>
                                        <p class="p-0 m-0"><strong>Contact #: </strong>{{ $delivery->contact }}</p>
                                        <p class="p-0 m-0"><strong>Code: </strong>{{ $delivery->code }}</p>
                                    </div>
                                    <!-- Grid column -->
                                    <!-- Grid column -->
                                    <div class="col-6 text-left">
                                        <p class="p-0 m-0"><strong>Consultant: </strong>{{ $delivery->consultant }}</p>
                                        <p class="p-0 m-0"><strong>Associate: </strong>{{ $delivery->associate }}</p>
                                        <p class="p-0 m-0"><strong>Presenter: </strong>{{ $delivery->presenter }}</p>
                                        <p class="p-0 m-0"><strong>Team Builder: </strong>{{ $delivery->teambuilder }}</p>
                                        <p class="p-0 m-0"><strong>Distributor: </strong>{{ $delivery->distributor }}</p>
                                    </div>
                                    <!-- Grid column -->
                                </div>
                                <div class="row">
                                    <font size="2" class="mt-0">
                                        <table class="table table-sm table-hover table-bordered mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-center">Quantity</th>
                                                    <th width="50%">Articles</th>
                                                    <th class="text-end">Unit Price</th>
                                                    <th class="text-end">Amount Due</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($delivery->items()->get() as $order)
                                                    <tr>
                                                        <td class="text-center">{{ $order->item_qty }}</td>
                                                        <td>{{ $order->item->item }} - <span
                                                                class="text-danger text-small">{{ $order->status }}</span>
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->item_price, 2) }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->item_total, 2) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4">-</td>
                                                    </tr>
                                                @endforelse
                                                @for ($row = 0; $row < $row_count; $row++)
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4"> -</td>
                                                    </tr>
                                                @endfor
                                                <tr class='table-light'>
                                                    <td class="text-end" colspan='3'><strong>SUBTOTAL:</strong></td>
                                                    <td class='text-end'><span>&#8369;
                                                        </span>{{ number_format($subtotal = $delivery->price_override ? $delivery->price_override : $delivery->items()->sum('item_total'), 2) }}
                                                    </td>
                                                </tr>
                                                <tr class='table-light'>
                                                    <td class="text-end" colspan='3'><strong>PRICE DIFFERENCE:</strong>
                                                    </td>
                                                    <td class='text-end'><span>&#8369;
                                                        </span>{{ number_format($price_diff = $delivery->price_diff, 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class='table-light'>
                                                    <td class="text-end" colspan='3'><strong>TOTAL:</strong></td>
                                                    <td class="text-end"><strong>&#8369;
                                                            {{ number_format((float) $subtotal + (float) $price_diff, 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <table class="table table-bordered table-sm m-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center" width="25%">
                                                        <p class="text-left"><strong>Delivered by: </strong></p></br></br>
                                                        <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                            over printed name</span>
                                                    </td>
                                                    <td class="text-center" width="25%">
                                                        <p class="text-left"><strong>Inspected Complete by: </strong></p>
                                                        </br></br>
                                                        <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                            over printed name</span>
                                                    </td>
                                                    <td class="text-center" width="25%">
                                                        <p class="text-left"><strong>Date Received: </strong></p></br></br>
                                                        <hr class="py-0 my-0">
                                                    </td>
                                                    <td class="text-center text-wrap text-center" width="25%">
                                                        <p><strong>Received in good order and condition by: </strong></p>
                                                        </br></br>
                                                        <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Customer
                                                            Signature over printed name</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered table-sm m-0">
                                            <thead>
                                                <tr class="low">
                                                    <th class="text-center fw-bold">PREPARED BY:</th>
                                                    <th class="text-center fw-bold">RELEASED BY:</th>
                                                    <th class="text-center fw-bold">APPROVED BY:</th>
                                                    <th class="text-center fw-bold">NOTED BY:</th>
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
                                                        <span class="py-0 my-0 font-small fw-bolder text-danger">--
                                                            Releasing Copy --</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </font>
                            </section>
                            <section class="page4">
                                <!-- Grid column -->
                                <div class="row mb-1">
                                    <div class="col-3 ps-4 mb-0">
                                        <img src="{{ url('img/str.png') }}" alt="" class="img-fluid"
                                            style="max-width: 60%">
                                    </div>
                                    <div class="col-6 text-center">
                                        <h3 class="fw-bold my-0">StrongNorth Cookware Trading</h3>
                                        <p class="small p-0 my-0">(Independent Authorized Dealer)</p>
                                        <p class="small p-0 my-0">9-10 VYV Bldg., Valdez Center, Brgy 1 San Nicolas, Ilocos
                                            Norte</p>
                                        <p class="small p-0 my-0">Contact: 0917-891-9180</p>
                                    </div>
                                    <div class="col-3 pe-4 mb-0 text-end">
                                        <img src="{{ url('img/right.png') }}" alt="" class="img-fluid"
                                            style="max-width: 60%">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        <h3 class="fw-bold my-0 py-0">DELIVERY RECEIPT</h3>
                                        <h5 class="h5 text-danger my-0 py-0">{{ $delivery->transno }}</h5>
                                        <hr class="my-1">
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Grid column -->
                                    <div class="col-6 text-left border-right">
                                        <p class="p-0 m-0"><strong>Date: </strong>{{ $delivery->date }}</p>
                                        <p class="p-0 m-0"><strong>Client: </strong>{{ $delivery->client }}</p>
                                        <p class="p-0 m-0"><strong>Address: </strong>{{ $delivery->address }}</p>
                                        <p class="p-0 m-0"><strong>Contact #: </strong>{{ $delivery->contact }}</p>
                                        <p class="p-0 m-0"><strong>Code: </strong>{{ $delivery->code }}</p>
                                    </div>
                                    <!-- Grid column -->
                                    <!-- Grid column -->
                                    <div class="col-6 text-left">
                                        <p class="p-0 m-0"><strong>Consultant: </strong>{{ $delivery->consultant }}</p>
                                        <p class="p-0 m-0"><strong>Associate: </strong>{{ $delivery->associate }}</p>
                                        <p class="p-0 m-0"><strong>Presenter: </strong>{{ $delivery->presenter }}</p>
                                        <p class="p-0 m-0"><strong>Team Builder: </strong>{{ $delivery->teambuilder }}</p>
                                        <p class="p-0 m-0"><strong>Distributor: </strong>{{ $delivery->distributor }}</p>
                                    </div>
                                    <!-- Grid column -->
                                </div>
                                <div class="row">
                                    <font size="2" class="mt-0">
                                        <table class="table table-sm table-hover table-bordered mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-center">Quantity</th>
                                                    <th width="50%">Articles</th>
                                                    <th class="text-end">Unit Price</th>
                                                    <th class="text-end">Amount Due</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($delivery->items()->get() as $order)
                                                    <tr>
                                                        <td class="text-center">{{ $order->item_qty }}</td>
                                                        <td>{{ $order->item->item }} - <span
                                                                class="text-danger text-small">{{ $order->status }}</span>
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->item_price, 2) }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($order->item_total, 2) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4">-</td>
                                                    </tr>
                                                @endforelse
                                                @for ($row = 0; $row < $row_count; $row++)
                                                    <tr>
                                                        <td class="text-center text-white" colspan="4"> -</td>
                                                    </tr>
                                                @endfor
                                                <tr class='table-light'>
                                                    <td class="text-end" colspan='3'><strong>SUBTOTAL:</strong></td>
                                                    <td class='text-end'><span>&#8369;
                                                        </span>{{ number_format($subtotal = $delivery->price_override ? $delivery->price_override : $delivery->items()->sum('item_total'), 2) }}
                                                    </td>
                                                </tr>
                                                <tr class='table-light'>
                                                    <td class="text-end" colspan='3'><strong>PRICE DIFFERENCE:</strong>
                                                    </td>
                                                    <td class='text-end'><span>&#8369;
                                                        </span>{{ number_format($price_diff = $delivery->price_diff, 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class='table-light'>
                                                    <td class="text-end" colspan='3'><strong>TOTAL:</strong></td>
                                                    <td class="text-end"><strong>&#8369;
                                                            {{ number_format((float) $subtotal + (float) $price_diff, 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <table class="table table-bordered table-sm m-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center" width="25%">
                                                        <p class="text-left"><strong>Delivered by: </strong></p></br></br>
                                                        <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                            over printed name</span>
                                                    </td>
                                                    <td class="text-center" width="25%">
                                                        <p class="text-left"><strong>Inspected Complete by: </strong></p>
                                                        </br></br>
                                                        <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Signature
                                                            over printed name</span>
                                                    </td>
                                                    <td class="text-center" width="25%">
                                                        <p class="text-left"><strong>Date Received: </strong></p></br></br>
                                                        <hr class="py-0 my-0">
                                                    </td>
                                                    <td class="text-center text-wrap text-center" width="25%">
                                                        <p><strong>Received in good order and condition by: </strong></p>
                                                        </br></br>
                                                        <hr class="py-0 my-0"><span class="py-0 my-0 font-small">Customer
                                                            Signature over printed name</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered table-sm m-0">
                                            <thead>
                                                <tr class="low">
                                                    <th class="text-center fw-bold">PREPARED BY:</th>
                                                    <th class="text-center fw-bold">RELEASED BY:</th>
                                                    <th class="text-center fw-bold">APPROVED BY:</th>
                                                    <th class="text-center fw-bold">NOTED BY:</th>
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
                                                        <span class="py-0 my-0 font-small fw-bolder text-danger">--
                                                            Client's Copy --</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </font>
                            </section>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="overrideTotalModal" tabindex="-1" aria-labelledby="overrideTotalModal"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="overrideTotalModal">Total Price Override</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="price_override" class="form-label">Total Price Override</label>
                            <input type="number" class="form-control" id="price_override" step="0.01"
                                wire:model.defer="price_override">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" wire:click="cancel_override()">Cancel
                            Override</button>
                        <button type="button" class="btn btn-primary" wire:click="override_price()">Submit</button>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="priceDifferenceModal" tabindex="-1" aria-labelledby="priceDifferenceModal"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="priceDifferenceModal">Price Difference</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="price_difference" class="form-label">Price Difference</label>
                            <input type="number" class="form-control" id="price_difference" step="0.01"
                                wire:model.defer="price_difference">
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
            function release(item_qty, item, item_id, type) {
                Swal.fire({
                    title: '<h5> Release ' + item_qty + ' ' + ' <strong>' + item + '</strong></h5>',
                    showCancelButton: true,
                    confirmButtonText: `Confirm`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        if (type == 1) {
                            Livewire.emit('release_item', item_id)
                        } else {
                            Livewire.emit('release_gift', item_id)
                        }
                    }
                });
            }

            function to_follow(item_qty, item, item_id, type) {
                Swal.fire({
                    title: '<h5> Set to follow ' + item_qty + ' ' + ' <strong>' + item + '</strong></h5>',
                    showCancelButton: true,
                    confirmButtonText: `Confirm`,
                    confirmButtonColor: '#fdae61',
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        if (type == 1) {
                            Livewire.emit('to_follow_item', item_id)
                        } else {
                            Livewire.emit('to_follow_gift', item_id)
                        }
                    }
                });
            }

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
