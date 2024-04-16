<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Office Supply List</span>
                <div class="d-flex w-25">
                    <div class="">
                        <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">New Office Supply</a>
                    </div>
                    <div class="col ms-2">
                        <div class="input-group">
                            <span class="input-group-text" id="search"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control form-control-sm" placeholder="Search" aria-label="Search" aria-describedby="search" wire:model.lazy="search">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Supply ID</th>
                            <th>Date Purchased</th>
                            <th>Category</th>
                            <th>Supply Name</th>
                            <th class="text-end">QTY</th>
                            <th class="text-end">Price</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($supplies as $row)
                            <tr>
                                <td>{{$row->id}}</td>
                                <td>{{$row->date_purchased}}</td>
                                <td>{{$row->category_name->name}}</td>
                                <td>{{$row->item_name}}</td>
                                <td class="text-end" onClick="dispose('{{$row->id}}', '{{$row->item_name}}', '{{$row->qty}}')" style="cursor: pointer">{{$row->qty}} {{$row->unit_name->unit}} <i class="fa-solid fa-trash text-danger"></i></td>
                                <td class="text-end" onClick="update_price('{{$row->id}}', '{{$row->unit_price}}', '{{$row->item_name}}')" style="cursor: pointer">{{number_format($row->unit_price,2)}} <i class="fa-solid fa-pen-to-square text-primary"></i></td>
                                <td>{{$row->location_name->name}}</td>
                            </tr>
                        @empty
                            <tr class="table-danger">
                                <td class="text-center" colspan="8">No Record Found!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <caption>{{$supplies->links()}}</caption>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">New Office Supply</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @error('date')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text">Date of Purchase</span>
                    <input type="date" class="form-control form-control-sm" wire:model.defer="date_purchased">
                </div>
                @error('location')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text">Location</span>
                    <select class="form-select form-select-sm" wire:model.defer="location">
                        <option value=""></option>
                        @foreach ($locations as $location)
                            <option value="{{$location->id}}">{{$location->name}}</option>
                        @endforeach
                    </select>
                </div>
                @error('category')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text">Category</span>
                    <select class="form-select form-select-sm" wire:model.defer="category">
                        <option value=""></option>
                        @foreach ($categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
                @error('item_name')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text">Item Description</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="item_name">
                </div>
                @error('unit')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text">Unit</span>
                    <select class="form-select form-select-sm" wire:model.defer="unit">
                        <option value=""></option>
                        @foreach ($measurements as $uom)
                            <option value="{{$uom->id}}">{{$uom->description}} - ({{$uom->unit}})</option>
                        @endforeach
                    </select>
                </div>
                @error('qty')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text">Initial QTY</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="qty">
                </div>
                @error('unit_price')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text w-25">Price</span>
                    <input type="number" min="0" class="form-control form-control-sm" wire:model.defer="unit_price">
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" wire:click="save()">Save</button>
            </div>
        </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function update_price(id, product_price, product_desc)
        {
            Swal.fire({
                title: '<h5> Update price of ' + ' <strong>' + product_desc + '</strong></h5>',
                html: `<div class="input-group mb-2">
                            <span class="input-group-text" id="up">Price</span>
                            <input id="new_price" type="number" step="0.01" class="form-control form-control-sm" aria-label="Price" aria-describedby="up">
                        </div>`,
                showCancelButton: true,
                confirmButtonText: `Confirm`,
                didOpen: () => {
                    const new_price = Swal.getHtmlContainer().querySelector('#new_price')
                    new_price.value = product_price;

                    new_price.addEventListener('input', () => {
                        @this.set('unit_price', new_price.value);
                    })
                }
            }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        Livewire.emit('update_price', id)
                    }
            });
        }

        function dispose(id, product_desc, maxim)
        {
            Swal.fire({
                title: '<h5> Dispose item: ' + ' <strong>' + product_desc + '</strong></h5>',
                html: `<div class="input-group mb-2">
                            <span class="input-group-text" id="up">Qty</span>
                            <input id="qty" type="number" min="1" max="`+ maxim +`" class="form-control form-control-sm" aria-label="qty" aria-describedby="qty">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="up">Reason</span>
                            <textarea id="reason" class="form-control"> </textarea>
                        </div>`,
                showCancelButton: true,
                confirmButtonText: `Confirm`,
                didOpen: () => {
                    const qty = Swal.getHtmlContainer().querySelector('#qty')
                    const reason = Swal.getHtmlContainer().querySelector('#reason')
                    @this.set('max', maxim);
                    qty.value = 0;

                    qty.addEventListener('input', () => {
                        @this.set('qty', qty.value);
                    })

                    qty.addEventListener('input', () => {
                        @this.set('reason', reason.value);
                    })
                }
            }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        Livewire.emit('dispose', id)
                    }
            });
        }
    </script>
@endpush
