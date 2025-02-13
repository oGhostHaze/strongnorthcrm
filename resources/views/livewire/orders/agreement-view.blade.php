<div class="container-fluid">
    <style>
        @media print {
            #print_div {
                font-size: 6px;
                /* Adjust the font size as needed */
            }
        }
    </style>
    @php
        $col = 7;
    @endphp
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header text-uppercase fw-bold">
                    <div class="d-flex justify-content-between">
                        <span>View Order #{{ $oa->oa_number }}</span>
                        <div class="d-flex">
                            @can('manage-orders')
                                @if (!$oa->drs()->count())
                                    <a href="" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                        data-bs-target="#addItemModal">Add Item</a>
                                    <a href="" class="btn btn-sm btn-warning ms-2 me-2" data-bs-toggle="modal"
                                        data-bs-target="#addGiftModal">Add Gift</a>

                                    <a href="" class="btn btn-sm btn-danger ms-5" data-bs-toggle="modal"
                                        data-bs-target="#priceDifferenceModal">Price Difference</a>
                                    <a href="" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal"
                                        data-bs-target="#overrideTotalModal">Override Total Price</a>
                                @endif
                            @endcan
                            <a class="btn btn-sm btn-primary me-2 ms-2" target="_blank"
                                href="{{ route('order.agreements.view.print', $oa->oa_number) }}"><i
                                    class="fas fa-print"></i>Preview</a>
                        </div>
                    </div>

                </div>
                <div class="card-body" style="" id='print_div'>
                    <!-- Grid column -->
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h3 class="font-weight-bold">ORDER AGREEMENT</h3>
                            <h7 class="font-weight-bold text-danger">{{ $oa->oa_number }}</h7>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Grid column -->
                        <div class="col-md-6 text-left border-right">
                            <p class="p-0 m-0"><strong>Date: </strong>{{ $oa->oa_date }}</p>
                            <p class="p-0 m-0"><strong>Client: </strong>{{ $oa->oa_client }}</p>
                            <p class="p-0 m-0"><strong>Address: </strong>{{ $oa->oa_address }}</p>
                            <p class="p-0 m-0"><strong>Contact #: </strong>{{ $oa->oa_contact }}</p>
                        </div>
                        <!-- Grid column -->
                        <!-- Grid column -->
                        <div class="col-md-6 text-left">
                            <p class="p-0 m-0"><strong>Consultant: </strong>{{ $oa->oa_consultant }}</p>
                            <p class="p-0 m-0"><strong>Associate: </strong>{{ $oa->oa_associate }}</p>
                            <p class="p-0 m-0"><strong>Presenter: </strong>{{ $oa->oa_presenter }}</p>
                            <p class="p-0 m-0"><strong>Team Builder: </strong>{{ $oa->oa_team_builder }}</p>
                            <p class="p-0 m-0"><strong>Distributor: </strong>{{ $oa->oa_distributor }}</p>
                        </div>
                        <!-- Grid column -->
                    </div>
                    <div class="row table-responsive">
                        <table class="table table-sm table-hover table-bordered">
                            <thead class="bg-light">

                                @error('return_qty')
                                    <tr class="table-danger">
                                        <th colspan="7">{{ $message }}</th>
                                    </tr>
                                @enderror
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Item Price</th>
                                    <th class="text-end">Pending</th>
                                    <th class="text-end">Released</th>
                                    <th class="text-end">Returned</th>
                                    <th class="text-end">Total</th>

                                    @can('manage-orders')
                                        @if (!$oa->drs()->count())
                                            <th></th>
                                        @endif
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($oa->items()->get() as $order)
                                    <tr {!! $order->tblset_id ? 'class="table-light"' : '' !!}>
                                        <td>{!! $order->item->tblset_id
                                            ? '<span class="fw-bold">' . $order->item->product_description . '</span> Composed of:'
                                            : $order->item->product_description !!}</td>
                                        <td class="text-end">{{ number_format($order->item_price, 2) }}</td>
                                        <td class="text-end">{{ $order->item_qty }}</td>
                                        <td class="text-end">{{ $order->released }}</td>
                                        <td class="text-end">{{ $order->returned }}</td>
                                        <td class="text-end">{{ number_format($order->item_total, 2) }}</td>
                                        @can('manage-orders')
                                            @if (!$oa->drs()->count())
                                                <td width="5%">
                                                    <a href="#" class="btn btn-sm btn-danger"
                                                        onclick="delete_item('{{ $order->item_id }}', '{{ $order->item->product_description }}')">Remove</a>
                                                </td>
                                            @endif
                                        @endcan
                                    </tr>
                                @empty
                                    @if ($oa->drs()->count())
                                        @php
                                            $col = 6;
                                        @endphp
                                    @endif
                                    <tr>
                                        <td class="text-center text-muted" colspan="{{ $col }}">No items found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <table class="table table-sm table-hover table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Gift</th>
                                    <th class="text-end">Item Price</th>
                                    <th class="text-end">Pending</th>
                                    <th class="text-end">Released</th>
                                    <th class="text-end">Returned</th>
                                    <th class="text-end">Total</th>

                                    @can('manage-orders')
                                        @if (!$oa->drs()->count())
                                            <th></th>
                                        @endif
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($oa->gifts()->get() as $order)
                                    <tr>
                                        <td>{{ $order->type }}</td>
                                        <td>{{ $order->gift->product_description }}</td>
                                        <td class="text-end">{{ number_format($order->item_price, 2) }}</td>
                                        <td class="text-end">{{ $order->item_qty }}</td>
                                        <td class="text-end">{{ $order->released }}</td>
                                        <td class="text-end">{{ $order->returned }}</td>
                                        <td class="text-end">{{ number_format(0, 2) }}</td>
                                        @can('manage-orders')
                                            @if (!$oa->drs()->count())
                                                @php
                                                    $col = 8;
                                                @endphp
                                                <td width="5%">
                                                    <a href="#" class="btn btn-sm btn-danger"
                                                        onclick="delete_gift('{{ $order->gift_id }}', '{{ $order->gift->product_description }}')">Remove</a>
                                                </td>
                                            @endif
                                        @endcan
                                    </tr>
                                @empty
                                    @if ($oa->drs()->count())
                                        @php
                                            $col = 7;
                                        @endphp
                                    @endif
                                    <tr>
                                        <td class="text-center text-muted" colspan="{{ $col }}">No gifts found
                                        </td>
                                    </tr>
                                @endforelse
                                <tr class='table-light'>
                                    <td class="text-end" colspan='6'><strong>SUBTOTAL:</strong></td>
                                    <td class='text-end' colspan="2"><span>&#8369;
                                        </span>{{ number_format($subtotal = $oa->oa_price_override ? $oa->oa_price_override : $oa->items()->sum('item_total'), 2) }}
                                    </td>
                                </tr>
                                <tr class='table-light'>
                                    <td class="text-end" colspan='6'><strong>PRICE DIFFERENCE:</strong></td>
                                    <td class='text-end' colspan="2"><span>&#8369;
                                        </span>{{ number_format($price_diff = $oa->oa_price_diff, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class='table-light'>
                                    <td class="text-end" colspan='6'><strong>TOTAL:</strong></td>
                                    <td class="text-end" colspan="2"><strong>&#8369;
                                            {{ $total = number_format((float) $subtotal + (float) $price_diff, 2) }}</strong>
                                    </td>
                                </tr>
                                <tr class='text-success'>
                                    <td class="text-end" colspan='6'><strong>TOTAL PAID:</strong></td>
                                    <td class="text-end" colspan="2"><strong>&#8369;
                                            {{ number_format($total_paid, 2) }}</strong>
                                    </td>
                                </tr>
                                <tr class='text-danger'>
                                    <td class="text-end" colspan='6'><strong>BALANCE:</strong></td>
                                    <td class="text-end" colspan="2"><strong>&#8369;
                                            {{ number_format((float) $subtotal + (float) $price_diff - $total_paid, 2) }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                    @php
                        $init = $initial ? $initial->amount : 0;
                        $total = (float) $subtotal + (float) $price_diff;
                    @endphp
                    <div class="row g-3 px-3 py-5 border">
                        <div class="col-6 d-flex flex-column">
                            <div class="d-flex">
                                <span>Delivery Date: {{ $oa->delivery_date }} </span>
                            </div>
                            <div class="d-flex">
                                <span>Time: {{ $oa->delivery__time }} </span>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="col-6 d-flex flex-column">
                            <div class="d-flex">
                                <span class="text-truncate">Current Spirit of Success Level: </span>
                                <span>{{ $oa->current_level }}</span>
                            </div>
                            <div class="d-flex">
                                <span>Initial Investment: </span>
                                <span class="ms-1">{{ number_format($init ?? 0, 2) }}</span>
                            </div>
                            <div class="d-flex">
                                <span>Terms: </span>
                                <span class="ms-1">{{ $oa->terms }}</span>
                            </div>
                            <p class="text-truncate">
                                <small>Checks payable only to <span class="fw-bold text-uppercase">StrongNorth
                                        Enterprise OPC</span></small>
                            </p>
                            <div class="d-flex flex-column justify-content-center pt-5 mt-10 text-center">
                                <div class="mx-auto">
                                    {{-- @if ($oa->host_signature)
                                        <img src="{{ url('upload/' . $oa->host_signature) }}" class="h-20" alt="Host Signature">
                                    @endif --}}
                                </div>
                                <div class="w-100 border-top">
                                    <span>Signature of Host</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist" wire:ignore.self>
                        <li class="nav-item" role="presentation" wire:ignore.self>
                            <button class="nav-link active" id="list-tab" data-bs-toggle="tab"
                                data-bs-target="#dr_list" type="button" role="tab" aria-controls="home"
                                aria-selected="true">List of DR</button>
                        </li>
                        @can('manage-orders-dr')
                            @if ($oa->items()->sum('item_qty') + $oa->gifts()->sum('item_qty') != 0 or $initial)
                                <li class="nav-item" role="presentation" wire:ignore.self>
                                    <button class="nav-link" id="new-tab" data-bs-toggle="tab"
                                        data-bs-target="#new_dr" type="button" role="tab" aria-controls="profile"
                                        aria-selected="false">New DR</button>
                                </li>
                            @endif
                            <li class="nav-item" role="presentation" wire:ignore.self>
                                <button class="nav-link" id="rsn-tab" data-bs-toggle="tab" data-bs-target="#rsn_list"
                                    type="button" role="tab" aria-controls="rsn_list" aria-selected="false">Return
                                    Slips</button>
                            </li>
                            <li class="nav-item" role="presentation" wire:ignore.self>
                                <button class="nav-link" id="payment-tab" data-bs-toggle="tab"
                                    data-bs-target="#payment_list" type="button" role="tab"
                                    aria-controls="payment_list" aria-selected="false">Payment History</button>
                            </li>
                        @endcan
                    </ul>
                    <div class="tab-content" id="myTabContent" wire:ignore.self>
                        <div class="tab-pane fade show active" id="dr_list" role="tabpanel"
                            aria-labelledby="dr_list" wire:ignore.self>
                            <table class="table table-sm table-hover table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>DR Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($oa->drs()->orderByDesc('dr_count')->get() as $dr)
                                        <tr wire:click="view_dr('{{ $dr->transno }}')" style="cursor: pointer">
                                            <td>
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <span
                                                            class="fw-bold text-primary">{{ $dr->transno }}</span><br>
                                                        <span class="text-small text-muted">{{ $dr->code }}</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span>{{ $dr->date }}</span><br>
                                                        <span><strong>Total Released:
                                                            </strong>{{ (int) $dr->items()->where('status', 'Released')->sum('item_qty') + (int) $dr->gifts()->where('status', 'Released')->sum('item_qty') }}</span><br>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">No Delivery Receipt Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <form class="tab-pane fade" id="new_dr" role="tabpanel" aria-labelledby="new_dr"
                            wire:submit.prevent="new_dr()" wire:ignore.self>
                            <div class="pt-3">
                                <div class="form-group mb-3">
                                    <label class="control-label" for="client">Client <span
                                            class="text-danger">*</span></label>
                                    <div id="ctrl-client-holder" class="">
                                        <input id="ctrl-client" wire:model.defer="delivery_client" type="text"
                                            placeholder="Enter Client" required="" class="form-control " />
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label" for="address">Address </label>
                                        <div id="ctrl-address-holder" class="">
                                            <input id="ctrl-address" wire:model.defer="delivery_address"
                                                type="text" placeholder="Enter Address" class="form-control " />
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="control-label" for="contact">Contact </label>
                                        <div id="ctrl-contact-holder" class="">
                                            <input id="ctrl-contact" wire:model.defer="delivery_contact"
                                                type="text" placeholder="Enter Contact" class="form-control " />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="control-label" for="consultant">Consultant </label>
                                    <div id="ctrl-consultant-holder" class="">
                                        <input id="ctrl-consultant" wire:model.defer="delivery_consultant"
                                            type="text" placeholder="Enter Consultant" class="form-control " />
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label" for="associate">Associate </label>
                                        <div id="ctrl-associate-holder" class="">
                                            <input id="ctrl-associate" wire:model.defer="delivery_assoc"
                                                type="text" placeholder="Enter Associate" class="form-control " />
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="control-label" for="presenter">Presenter </label>
                                        <div id="ctrl-presenter-holder" class="">
                                            <input id="ctrl-presenter" wire:model.defer="delivery_presenter"
                                                type="text" placeholder="Enter Presenter" class="form-control " />
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label" for="team_builder">Team Builder </label>
                                        <div id="ctrl-team_builder-holder" class="">
                                            <input id="ctrl-team_builder" wire:model.defer="delivery_tb"
                                                type="text" placeholder="Enter Team Builder"
                                                class="form-control " />
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="control-label" for="distributor">Distributor </label>
                                        <div id="ctrl-distributor-holder" class="">
                                            <input id="ctrl-distributor" wire:model.defer="delivery_distributor"
                                                type="text" placeholder="Enter Distributor"
                                                class="form-control " />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="control-label" for="code">Code <span
                                            class="text-danger">*</span></label>
                                    <div id="ctrl-code-holder" class="">
                                        <select id="ctrl-code" wire:model.defer="delivery_code"
                                            placeholder="Select a value ..." class="form-select">
                                            <option value="">Select a value ...</option>
                                            <option value="NEW ORDER">NEW ORDER</option>
                                            <option value="TRIAL DELIVERY">TRIAL DELIVERY</option>
                                            {{-- <option value="TFO">TFO</option>
                                            <option value="TFG">TFG</option>
                                            <option value="INCR">INCR</option>
                                            <option value="INCR+">INCR+</option>
                                            <option value="INC-CGP">INC-CGP</option>
                                            <option value="INC-CGPEXTRA">INC-CGPEXTRA</option>
                                            <option value="INC-DIGI">INC-DIGI</option>
                                            <option value="INC-LMC">INC-LMC</option>
                                            <option value="WARRANTY REPLACEMENT">WARRANTY REPLACEMENT</option>
                                            <option value="LTD-WTY">LTD-WTY</option> --}}
                                            <option value="DEMO KIT">GIFT</option>
                                            <option value="DEMO KIT">DEMO KIT</option>
                                            <option value="DISPLAY">DISPLAY</option>
                                        </select>
                                    </div>
                                    @error('delivery_code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group form-submit-btn-holder text-center mt-3">
                                <div class="form-ajax-status"></div>
                                <button class="btn btn-primary" type="submit">
                                    Submit
                                    <i class="fa fa-send"></i>
                                </button>
                            </div>
                        </form>
                        <div class="tab-pane fade" id="rsn_list" role="tabpanel" aria-labelledby="rsn_list"
                            wire:ignore.self>
                            <table class="table table-sm table-hover table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>
                                            Return Slips
                                            <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                                                data-bs-target="#newRsnModal">New RSN</button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($oa->returns()->orderByDesc('id')->get() as $return)
                                        <tr wire:click="view_rsn('{{ $return->id }}')" style="cursor: pointer">
                                            <td>
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <span
                                                            class="fw-bold text-primary">RSN-{{ $return->id }}</span>
                                                        <span
                                                            class="badge text-black-50">{{ $return->dr_no }}</span><br>
                                                        <span
                                                            class="badge text-small {{ $return->status == 'Approved' ? 'bg-success' : 'bg-primary' }}">{{ $return->status }}</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span>{{ $return->created_at }}</span><br>
                                                        <span><strong>Total Items:
                                                            </strong>{{ (int) $return->return_items()->sum('qty') }}</span><br>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">No Return Slip Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="payment_list" role="tabpanel" aria-labelledby="payment_list"
                            wire:ignore.self>
                            <table class="table table-sm table-hover table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>
                                            <div class="d-flex justify-content-between">
                                                Payment History
                                                <div class="fload-end d-flex justify-content-end">
                                                    <a href="{{ route('order.agreements.batch-add-payments', ['oa_id' => $oa->oa_id]) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-primary float-end ms-2">Add Payment</a>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr style="cursor: pointer"
                                            onclick="update_payment(`{{ $payment->id }}`, `{{ $payment->status }}`, `{{ $payment->remarks }}`)">
                                            <td>
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <span
                                                            class="fw-bold text-primary">{{ number_format($payment->amount, 2) }}</span><br>
                                                        <span
                                                            class="badge text-small bg-secondary">{{ $payment->mop }}</span>
                                                        @if ($payment->remarks)
                                                            <br>
                                                            <p>{{ $payment->remarks }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-end">
                                                        <span>{{ $payment->date_of_payment }}</span><br>
                                                        <span
                                                            class="badge text-small @if ($payment->status == 'Unposted') bg-secondary @elseif($payment->status == 'Posted') bg-primary @elseif($payment->status == 'Commissioned') bg-success @elseif($payment->status == 'On Hold') bg-danger @endif">{{ $payment->status }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">No Payment Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{-- Add Item Modal --}}
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModal" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModal">Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" wire:ignore>
                        <label for="item_id" class="form-label">Product</label><br>
                        <select class="form-control" id="item_id" wire:model.defer="item_id" style="width: 100%;">
                            <option value=""></option>
                            @foreach ($products as $product)
                                <option value="{{ $product->product_id }}">{{ $product->product_description }} | Qty:
                                    {{ $product->product_qty }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="item_qty" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="item_qty" step="1"
                            wire:model.defer="item_qty">
                    </div>
                    <div class="mb-3">
                        <label for="item_remarks" class="form-label">Remarks</label>
                        <input type="text" class="form-control" id="item_remarks"
                            wire:model.defer="item_remarks">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="add_item()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Add Item Modal --}}

    {{-- Add Gift Modal --}}
    <div class="modal fade" id="addGiftModal" tabindex="-1" aria-labelledby="addGiftModal" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGiftModal">Add Gift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" wire:ignore>
                        <label for="gift_id" class="form-label">Product</label><br>
                        <select class="form-control" id="gift_id" wire:model.defer="gift_id" style="width: 100%;">
                            <option value=""></option>
                            @foreach ($products as $product)
                                <option value="{{ $product->product_id }}">{{ $product->product_description }} | Qty:
                                    {{ $product->product_qty }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="gift_qty" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="gift_qty" step="1"
                            wire:model.defer="gift_qty">
                    </div>
                    <div class="mb-3">
                        <label for="gift_type" class="form-label">Type</label>
                        <input type="text" class="form-control" id="gift_type" wire:model.defer="gift_type">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="add_gift()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Add Gift Modal --}}

    {{-- Price Override --}}
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
    {{-- Price Override --}}

    {{-- Price Difference --}}
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
    {{-- Price Difference --}}

    {{-- New RSN --}}
    <div class="modal fade" id="newRsnModal" tabindex="-1" aria-labelledby="newRsnModal" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newRsnModal">New RSN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" wire:ignore>
                        <label for="rsn_dr" class="form-label">DR</label><br>
                        <select class="form-control" id="rsn_dr" wire:model.defer="rsn_dr" style="width: 100%;">
                            <option value=""></option>
                            @foreach ($oa->drs()->orderByDesc('dr_count')->get() as $dr)
                                <option value="{{ $dr->transno }}">{{ $dr->transno }} [{{ $dr->code }}] Total
                                    Released:
                                    {{ (int) $dr->items()->where('status', 'Released')->sum('item_qty') + (int) $dr->gifts()->where('status', 'Released')->sum('item_qty') }}
                                </option>
                            @endforeach
                        </select>
                        @error('rsn_dr')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click="new_rsn()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    {{-- New RSN --}}

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
                    <div class="mb-3">
                        <label for="remarks">Remarks</label>
                        <textarea type="text" class="form-control" id="remarks" wire:model="remarks"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click="update_payment()"
                        data-bs-dismiss="modal" aria-label="Close">Submit</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Update Payment --}}
</div>

@push('scripts')
    <script>
        function update_payment(id, status, remarks) {
            @this.set('payment_id', id);
            @this.set('status', status);
            @this.set('remarks', remarks);

            $('#updatePaymentModal').modal('toggle');
        }

        $('#item_id').select2({
            dropdownParent: $('#addItemModal'),
            width: 'resolve'
        });

        $('#item_id').on('change', function(e) {
            var item_id = $('#item_id').select2("val");
            @this.set('item_id', item_id);
        });

        $('#gift_id').select2({
            dropdownParent: $('#addGiftModal'),
            width: 'resolve'
        });
        $('#gift_id').on('change', function(e) {
            var gift_id = $('#gift_id').select2("val");
            @this.set('gift_id', gift_id);
        });

        window.addEventListener('item_added', event => {
            $('#addItemModal').modal('toggle');
        });

        window.addEventListener('gift_added', event => {
            $('#addGiftModal').modal('toggle');
        });

        function delete_item(remove_id, product_desc) {
            Swal.fire({
                title: '<h5> Remove ' + ' <strong>' + product_desc + '</strong> from order items</h5>',
                showCancelButton: true,
                confirmButtonText: `Confirm`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    Livewire.emit('remove_item', remove_id)
                }
            });
        }

        function delete_gift(remove_id, product_desc) {
            Swal.fire({
                title: '<h5> Delete ' + ' <strong>' + product_desc + '</strong> from order gifts</h5>',
                showCancelButton: true,
                confirmButtonText: `Confirm`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    Livewire.emit('remove_gift', remove_id)
                }
            });
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
