<div class="container-fluid">
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
                        </div>
                    </div>

                </div>
                <div class="card-body">
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
                                    {{-- <th>Status</th> --}}
                                    @can('manage-orders')
                                        <th></th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($oa->items()->get() as $order)
                                    {{-- @php
                                    if($order->tblset_id){
                                        $oa->items()->sum('item_qty')
                                    }
                                @endphp --}}
                                    <tr {!! $order->tblset_id ? 'class="table-light"' : '' !!}>
                                        <td>{!! $order->item->tblset_id
                                            ? '<span class="fw-bold">' . $order->item->product_description . '</span> Composed of:'
                                            : $order->item->product_description !!}</td>
                                        <td class="text-end">{{ number_format($order->item_price, 2) }}</td>
                                        <td class="text-end">{{ $order->item_qty }}</td>
                                        <td class="text-end">{{ $order->released }}</td>
                                        <td class="text-end">{{ $order->returned }}</td>
                                        <td class="text-end">{{ number_format($order->item_total, 2) }}</td>
                                        {{-- <td>{{$order->status}}</td> --}}
                                        @can('manage-orders')
                                            <td width="5%">
                                                {{-- @if ($order->released == 0 and $order->returned == 0 and !$order->tblset_id) --}}
                                                @if (!$oa->drs()->count())
                                                    <a href="#" class="btn btn-sm btn-danger"
                                                        onclick="delete_item('{{ $order->item_id }}', '{{ $order->item->product_description }}')">Remove</a>
                                                    {{-- @elseif($order->released != 0 AND  !$order->tblset_id)
                                                <a href="#" class="btn btn-sm btn-warning" onclick="return_item('{{$order->product_id}}', '{{$order->item_id}}', '{{$order->item->product_description}}', '{{$order->item_price}}', '{{$order->released}}')">Return</a> --}}
                                                @endif
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted" colspan="7">No items found</td>
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
                                    {{-- <th>Status</th> --}}
                                    @can('manage-orders')
                                        <th></th>
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
                                        {{-- <td>{{$order->status}}</td> --}}
                                        @can('manage-orders')
                                            <td width="5%">
                                                {{-- @if ($order->released == 0 and $order->returned == 0) --}}
                                                @if (!$oa->drs()->count())
                                                    <a href="#" class="btn btn-sm btn-danger"
                                                        onclick="delete_gift('{{ $order->gift_id }}', '{{ $order->gift->product_description }}')">Remove</a>
                                                    {{-- @elseif($order->released != 0)
                                                <a href="#" class="btn btn-sm btn-warning" onclick="return_gift('{{$order->product_id}}', '{{$order->gift_id}}', '{{$order->gift->product_description}}', '{{$order->item_price}}', '{{$order->released}}')">Return</a> --}}
                                                @endif
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted" colspan="8">No gifts found</td>
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
                                            {{ number_format((float) $subtotal + (float) $price_diff, 2) }}</strong>
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
                            @if ($oa->items()->sum('item_qty') + $oa->gifts()->sum('item_qty') != 0)
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
                                                            </strong>{{ (int) $dr->items()->where('status', 'Released')->sum('item_qty') +(int) $dr->gifts()->where('status', 'Released')->sum('item_qty') }}</span><br>
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
                                            <option value="TFO">TFO</option>
                                            <option value="TFG">TFG</option>
                                            <option value="INCR">INCR</option>
                                            <option value="INCR+">INCR+</option>
                                            <option value="INC-CGP">INC-CGP</option>
                                            <option value="INC-CGPEXTRA">INC-CGPEXTRA</option>
                                            <option value="INC-DIGI">INC-DIGI</option>
                                            <option value="INC-LMC">INC-LMC</option>
                                            <option value="WARRANTY REPLACEMENT">WARRANTY REPLACEMENT</option>
                                            <option value="LTD-WTY">LTD-WTY</option>
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
                                            <button class="btn btn-sm btn-primary float-end"
                                                wire:click="new_rsn()">New RSN</button>
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
                                                            class="fw-bold text-primary">RSN-{{ $return->id }}</span><br>
                                                        <span
                                                            class="badge text-small {{ $return->status == 'Approved' ? 'bg-success' : 'bg-light' }}">{{ $return->status }}</span>
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
                                            Payment History
                                            <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
                                                data-bs-target="#paymentModal">Add Payment</button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr style="cursor: pointer">
                                            <td>
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <span
                                                            class="fw-bold text-primary">{{ number_format($payment->amount, 2) }}</span><br>
                                                        <span
                                                            class="badge text-small bg-secondary">{{ $payment->mop }}</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span>{{ $payment->date_of_payment }}</span>
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

    {{-- New Payment --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModal" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModal">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mop" class="form-label">Mode of Payment</label>
                        <input type="text" class="form-control" id="mop" wire:model.defer="mop">
                    </div>
                    <div class="mb-3">
                        <label for="date_of_payment" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date_of_payment"
                            wire:model.defer="date_of_payment">
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" step="0.01"
                            wire:model.defer="amount">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click="add_payment()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    {{-- New Payment --}}
</div>


@push('scripts')
    <script>
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

        // function return_item(product_id, return_id, item_desc, up, order_qty)
        // {
        //     @this.set('return_product', product_id);
        //     @this.set('return_price', up);
        //     Swal.fire({
        //         title: '<h5> Return' + ' Item <strong>' + item_desc + '</strong></h5>',
        //         html: `
    //                 <div class="input-group mb-2">
    //                     <span class="input-group-text" id="qty">Issued Qty</span>
    //                     <input id="ordered_qty" type="number" class="form-control form-control-sm" aria-label="Qty" aria-describedby="qty" readonly tabindex='-1'>
    //                 </div>

    //                 <div class="input-group mb-2">
    //                     <span class="input-group-text" id="ret_label">Return Qty</span>
    //                     <input id="return_qty" type="number" min="1" max="`+order_qty+`"class="form-control form-control-sm" aria-label="Return Qty" aria-describedby="ret_label" autofocus>
    //                 </div>

    //                 <div class="input-group mb-2">
    //                     <span class="input-group-text" id="up">Unit Price</span>
    //                     <input id="unit_price" type="number" step="0.01" class="form-control form-control-sm" aria-label="Unit Price" readonly tabindex='-1' aria-describedby="up" >
    //                 </div>

    //                 <div class="input-group mb-2">
    //                     <span class="input-group-text" id="total_label">TOTAL</span>
    //                     <input id="total" type="number" step="0.01" class="form-control form-control-sm" aria-label="Unit Price" aria-describedby="total" readonly tabindex='-1'>
    //                 </div>
    //                     `,
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         confirmButtonText: `Confirm`,
        //         didOpen: () => {
        //             const ordered_qty = Swal.getHtmlContainer().querySelector('#ordered_qty')
        //             const return_qty = Swal.getHtmlContainer().querySelector('#return_qty')
        //             const unit_price = Swal.getHtmlContainer().querySelector('#unit_price')
        //             const total = Swal.getHtmlContainer().querySelector('#total')
        //             ordered_qty.value = order_qty
        //             unit_price.value = up
        //             total.value = parseFloat(ordered_qty.value) * parseFloat(unit_price.value)
        //             return_qty.focus()
        //             @this.set('return_id',return_id)

        //             return_qty.addEventListener('input', () => {
        //                 @this.set('return_qty',return_qty.value)
        //                 total.value = parseFloat(return_qty.value) * parseFloat(unit_price.value)
        //             })
        //         }
        //     }).then((result) => {
        //             /* Read more about isConfirmed, isDenied below */
        //             if (result.isConfirmed) {
        //                 Livewire.emit('return_item', return_id)
        //             }
        //     });
        // }

        // function return_gift(product_id, return_id, item_desc, up, order_qty)
        // {
        //     @this.set('return_product', product_id);
        //     @this.set('return_price', up);
        //     Swal.fire({
        //         title: '<h5> Return' + ' Gift<strong> ' + item_desc + '</strong></h5>',
        //         html: `
    //                 <div class="input-group mb-2">
    //                     <span class="input-group-text" id="qty">Issued Qty</span>
    //                     <input id="ordered_qty" type="number" class="form-control form-control-sm" aria-label="Qty" aria-describedby="qty" readonly tabindex='-1'>
    //                 </div>

    //                 <div class="input-group mb-2">
    //                     <span class="input-group-text" id="ret_label">Return Qty</span>
    //                     <input id="return_qty" type="number" min="1" max="`+order_qty+`"class="form-control form-control-sm" aria-label="Return Qty" aria-describedby="ret_label" autofocus>
    //                 </div>

    //                 <div class="input-group mb-2">
    //                     <span class="input-group-text" id="up">Unit Price</span>
    //                     <input id="unit_price" type="number" step="0.01" class="form-control form-control-sm" aria-label="Unit Price" readonly tabindex='-1' aria-describedby="up" >
    //                 </div>

    //                 <div class="input-group mb-2">
    //                     <span class="input-group-text" id="total_label">TOTAL</span>
    //                     <input id="total" type="number" step="0.01" class="form-control form-control-sm" aria-label="Unit Price" aria-describedby="total" readonly tabindex='-1'>
    //                 </div>
    //                     `,
        //         showCancelButton: true,
        //         confirmButtonColor: '#d33',
        //         confirmButtonText: `Confirm`,
        //         didOpen: () => {
        //             const ordered_qty = Swal.getHtmlContainer().querySelector('#ordered_qty')
        //             const return_qty = Swal.getHtmlContainer().querySelector('#return_qty')
        //             const unit_price = Swal.getHtmlContainer().querySelector('#unit_price')
        //             const total = Swal.getHtmlContainer().querySelector('#total')
        //             ordered_qty.value = order_qty
        //             unit_price.value = up
        //             total.value = parseFloat(ordered_qty.value) * parseFloat(unit_price.value)
        //             return_qty.focus()
        //             @this.set('return_id',return_id)

        //             return_qty.addEventListener('input', () => {
        //                 @this.set('return_qty',return_qty.value)
        //                 total.value = parseFloat(return_qty.value) * parseFloat(unit_price.value)
        //             })
        //         }
        //     }).then((result) => {
        //             /* Read more about isConfirmed, isDenied below */
        //             if (result.isConfirmed) {
        //                 Livewire.emit('return_gift', return_id)
        //             }
        //     });
        // }

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
    </script>
@endpush
