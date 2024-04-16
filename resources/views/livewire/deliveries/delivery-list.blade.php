<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Delivery Receipts</span>
                <div class="d-flex w-25">
                    <div class="">
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
                            <th>Date</th>
                            <th>DR #</th>
                            <th>Client</th>
                            <th class="text-end">Contact #</th>
                            <th>Lifechanger</th>
                            <th>Partner</th>
                            <th>Presenter</th>
                            <th>Items</th>
                            <th>Gift</th>
                            <th>Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                            <tr wire:click="view_dr('{{$row->transno}}')" style="cursor: pointer">
                                <td>{{$row->date}}</td>
                                <td>{{$row->transno}}</td>
                                <td>{{$row->client}}</td>
                                <td>{{$row->contact}}</td>
                                <td>{{$row->consultant}}</td>
                                <td>{{$row->associate}}</td>
                                <td>{{$row->presenter}}</td>
                                <td>{{$row->items()->sum('item_qty')}}</td>
                                <td>{{$row->gifts()->sum('item_qty')}}</td>
                                <td>{{$row->code}}</td>
                            </tr>
                        @empty

                        @endforelse
                    </tbody>
                </table>
            </div>
            <caption>{{$data->links()}}</caption>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function update_price(product_id, product_price, product_desc)
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
                        @this.set('product_price', new_price.value);
                    })
                }
            }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        Livewire.emit('update_price', product_id)
                    }
            });
        }
    </script>
@endpush
