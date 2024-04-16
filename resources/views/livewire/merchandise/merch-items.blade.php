<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Merchandise Items List</span>
                <div class="d-flex w-25">
                    <div class="">
                        <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">New Item</a>
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
                            <th>Merch ID</th>
                            <th>Merchandise</th>
                            <th class="text-end">QTY</th>
                            <th class="text-end">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($merchs as $row)
                            <tr>
                                <td>{{$row->id}}</td>
                                <td>{{$row->item}}</td>
                                <td class="text-end">{{$row->qty}}</td>
                                <td class="text-end" onClick="update_price('{{$row->id}}', '{{$row->price}}', '{{$row->item}}')" style="cursor: pointer">{{number_format($row->price,2)}} <i class="fa-solid fa-pen-to-square text-primary"></i></td>
                            </tr>
                        @empty
                            <tr class="table-danger">
                                <td class="text-center" colspan="4">No Record Found!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <caption>{{$merchs->links()}}</caption>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">New Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @error('item')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text">Item Description</span>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="item">
                </div>
                @error('price')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                <div class="input-group mb-2">
                    <span class="input-group-text w-25">Price</span>
                    <input type="number" min="0" class="form-control form-control-sm" wire:model.defer="price">
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
                        @this.set('price', new_price.value);
                    })
                }
            }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        Livewire.emit('update_price', id)
                    }
            });
        }
    </script>
@endpush
