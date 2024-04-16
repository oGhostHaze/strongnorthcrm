<div class="container-fluid">
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
            <div class="card">
                <div class="card-header text-uppercase fw-bold">
                    <div class="d-flex justify-content-between">
                        <span>View Order #{{ $oa->number }}</span>
                        <div class="d-flex">
                            @if ($oa->status != 'Approved')
                                @can('manage-orders')
                                    <a href="" class="btn btn-sm btn-success ms-2" data-bs-toggle="modal"
                                        data-bs-target="#approve">Approve Order</a>
                                    <a href="" class="btn btn-sm btn-info ms-2 me-5" data-bs-toggle="modal"
                                        data-bs-target="#additionalDetails">Update Additional Details</a>
                                    <a href="" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                        data-bs-target="#addItemModal">Add Item</a>
                                    <a href="" class="btn btn-sm btn-warning ms-2 me-2" data-bs-toggle="modal"
                                        data-bs-target="#addGiftModal">Add Gift</a>

                                    <a href="" class="btn btn-sm btn-danger ms-5" data-bs-toggle="modal"
                                        data-bs-target="#priceDifferenceModal">Price Difference</a>
                                    <a href="" class="btn btn-sm btn-secondary ms-2" data-bs-toggle="modal"
                                        data-bs-target="#overrideTotalModal">Override Total Price</a>
                                @endcan
                            @endif
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <!-- Grid column -->
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h3 class="font-weight-bold">ORDER AGREEMENT</h3>
                            @if ($oa->final_oa)
                                <h7 class="font-weight-bold text-danger">{{ $oa->final_oa->oa_number }}</h7>
                            @else
                                <h7 class="font-weight-bold text-danger">OA ref ID: #{{ $oa->id }}</h7>
                            @endif
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Grid column -->
                        <div class="col-md-6 text-left border-right">
                            <p class="p-0 m-0"><strong>Date: </strong>{{ $oa->date }}</p>
                            <p class="p-0 m-0"><strong>Client: </strong>{{ $oa->client }}</p>
                            <p class="p-0 m-0"><strong>Address: </strong>{{ $oa->address }}</p>
                            <p class="p-0 m-0"><strong>Contact #: </strong>{{ $oa->contact }}</p>
                        </div>
                        <!-- Grid column -->
                        <!-- Grid column -->
                        <div class="col-md-6 text-left">
                            <p class="p-0 m-0"><strong>Consultant: </strong>{{ $oa->consultant }}</p>
                            <p class="p-0 m-0"><strong>Associate: </strong>{{ $oa->associate }}</p>
                            <p class="p-0 m-0"><strong>Presenter: </strong>{{ $oa->presenter }}</p>
                            <p class="p-0 m-0"><strong>Team Builder: </strong>{{ $oa->team_builder }}</p>
                            <p class="p-0 m-0"><strong>Distributor: </strong>{{ $oa->distributor }}</p>
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
                                    @if ($oa->status != 'Approved')
                                        @can('manage-orders')
                                            <th></th>
                                        @endcan
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($oa->items()->get() as $order)
                                    <tr {!! $order->tblset_id ? 'class="table-light"' : '' !!}>
                                        <td>{!! $order->product->tblset_id
                                            ? '<span class="fw-bold">' . $order->product->product_description . '</span> Composed of:'
                                            : $order->product->product_description !!}</td>
                                        <td class="text-end">{{ number_format($order->item_price, 2) }}</td>
                                        <td class="text-end">{{ $order->item_qty }}</td>
                                        <td class="text-end">{{ $order->released }}</td>
                                        <td class="text-end">{{ $order->returned }}</td>
                                        <td class="text-end">{{ number_format($order->item_total, 2) }}</td>
                                        @if ($oa->status != 'Approved')
                                            @can('manage-orders')
                                                <td width="5%">
                                                    <a href="#" class="btn btn-sm btn-danger"
                                                        onclick="delete_item('{{ $order->id }}', '{{ $order->product->product_description }}', 'item')">Remove</a>
                                                </td>
                                            @endcan
                                        @endif
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
                                    @if ($oa->status != 'Approved')
                                        @can('manage-orders')
                                            <th></th>
                                        @endcan
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($oa->gifts()->get() as $order)
                                    <tr>
                                        <td>{{ $order->type }}</td>
                                        <td>{{ $order->product->product_description }}</td>
                                        <td class="text-end">{{ number_format($order->item_price, 2) }}</td>
                                        <td class="text-end">{{ $order->item_qty }}</td>
                                        <td class="text-end">{{ $order->released }}</td>
                                        <td class="text-end">{{ $order->returned }}</td>
                                        <td class="text-end">{{ number_format(0, 2) }}</td>
                                        {{-- <td>{{$order->status}}</td> --}}
                                        @if ($oa->status != 'Approved')
                                            @can('manage-orders')
                                                <td width="5%">
                                                    <a href="#" class="btn btn-sm btn-danger"
                                                        onclick="delete_gift('{{ $order->id }}', '{{ $order->product->product_description }}', 'gift')">Remove</a>
                                                </td>
                                            @endcan
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted" colspan="8">No gifts found</td>
                                    </tr>
                                @endforelse
                                <tr class='table-light'>
                                    <td class="text-end" colspan='6'><strong>SUBTOTAL:</strong></td>
                                    <td class='text-end' colspan="2"><span>&#8369;
                                        </span>{{ number_format($subtotal = $oa->price_override ? $oa->price_override : $oa->items()->sum('item_total'), 2) }}
                                    </td>
                                </tr>
                                <tr class='table-light'>
                                    <td class="text-end" colspan='6'><strong>PRICE DIFFERENCE:</strong></td>
                                    <td class='text-end' colspan="2"><span>&#8369;
                                        </span>{{ number_format($price_diff = $oa->price_diff, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class='table-light'>
                                    <td class="text-end" colspan='6'><strong>TOTAL:</strong></td>
                                    <td class="text-end" colspan="2"><strong>&#8369;
                                            {{ number_format((float) $subtotal + (float) $price_diff, 2) }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                    @php
                        $total = (float) $subtotal + (float) $price_diff;
                    @endphp
                    <div class="row">
                        <div class="row col-6">
                            <div class="row">
                                <span>Delivery Date: {{ $oa->delivery_date }} </span>
                            </div>
                            <div class="row">
                                <span>Time: {{ $oa->delivery_time }} </span>
                            </div>
                            <div class="row">
                                <span>Total Amount: {{ number_format($total, 2) }} </span>
                            </div>
                            <br><br><br><br><br><br>
                            <br><br><br><br><br>
                        </div>
                        <div class="row col-6">
                            <div class="row">
                                <span class=" text-ellipsis">Current Spirit of Success Level:
                                    {{ $oa->current_level }}</span>
                            </div>
                            <div class="row">
                                <span>Initial Investment: {{ number_format($oa->initial_investment, 2) }}</span>
                            </div>
                            <div class="row">
                                <span>Balance: {{ number_format($total - $oa->initial_investment, 2) }}</span>
                            </div>
                            <div class="row">
                                <span>Terms: {{ $oa->terms }}</span>
                            </div>
                            <p class="text-ellipsis">
                                <small>Checks payable only to <span class="font-bold uppercase">StrongNorth Cookware
                                        Trading</span></small>
                            </p>
                            <div class="flex flex-col justify-center pt-5 mt-10 text-center">
                                <div class="mx-auto">
                                    @if ($oa->host_signature)
                                        <img src="{{ 'http://strongnorthoa.test/upload/' . $oa->host_signature }}"
                                            style="height: 100px" alt="Host Signature">
                                    @endif
                                </div>
                                <div class="w-full border-t">
                                    <span>Signature of Host</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-2"></div>
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
    <div class="modal fade" id="additionalDetails" tabindex="-1" aria-labelledby="additionalDetails"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="additionalDetails">Update Additional Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <span class="form-label">Current Level</span>
                        <select class="form-select" wire:model='current_level'>
                            <option value="Associate">Associate</option>
                            <option value="Consultant">Consultant</option>
                            <option value="Senior Consultant">Senior Consultant</option>
                            <option value="Distributor">Distributor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <span class="form-label">Delivery Date</span>
                        <input type="date" min="{{ date('Y-m-d') }}" class="form-control"
                            wire:model="delivery_date" />
                    </div>
                    <div class="mb-3">
                        <span class="form-label">Delivery Time</span>
                        <input type="time" class="form-control" wire:model="delivery_time" />
                    </div>
                    <div class="mb-3">
                        <span class="text-sm form-label">Initial Investment</span>
                        <input type="number" step="0.01" class="form-control" wire:model="initial_investment" />
                    </div>
                    <div class="mb-3">
                        <span class="w-1/4 form-label">Terms</span>
                        <input type="text" class="form-control" wire:model="terms" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click="update_details()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    {{-- New Payment --}}

    {{-- Approve OA --}}
    <div class="modal fade" id="approve" tabindex="-1" aria-labelledby="approve" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approve">Approve Order Agreement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <span class="form-label">Submit for delivery receipt creation.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click="approve()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Approve OA --}}
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

        function delete_item(remove_id, product_desc, type) {
            Swal.fire({
                title: '<h5> Remove ' + ' <strong>' + product_desc + '</strong> from order items</h5>',
                showCancelButton: true,
                confirmButtonText: `Confirm`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    Livewire.emit('remove_item', remove_id, type)
                }
            });
        }

        function delete_gift(remove_id, product_desc, type) {
            Swal.fire({
                title: '<h5> Delete ' + ' <strong>' + product_desc + '</strong> from order gifts</h5>',
                showCancelButton: true,
                confirmButtonText: `Confirm`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    Livewire.emit('remove_item', remove_id, type)
                }
            });
        }
    </script>
@endpush
