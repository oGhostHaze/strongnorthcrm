<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">Product List</span>
                <div class="d-flex w-50">
                    <div class="">
                        @can('create-product')
                            <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                                    class="far fa-plus me-1"></i>New Product</a>
                        @endcan

                        <button wire:click="download_csv()" class="btn btn-sm btn-primary"><i
                                class="far fa-file-excel me-1"></i> Export CSV</button>

                        <button wire:click="send_report()" class="btn btn-sm btn-primary"><i
                                class="far fa-paper-plane me-1"></i> Send Report</button>
                    </div>
                    <div class="col ms-2">
                        <div class="input-group">
                            <span class="input-group-text" id="search"><i
                                    class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control form-control-sm" placeholder="Search"
                                aria-label="Search" aria-describedby="search" wire:model.lazy="search">
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
                            <th>Product ID</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th class="text-end">QTY</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">SPV</th>
                            <th class="text-end">Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                            <tr>
                                <td>{{ $row->product_id }}</td>
                                <td>{{ $row->code }}</td>
                                <td>{{ $row->product_description }}</td>
                                <td class="text-end">
                                    {{ $row->product_qty }}
                                    @if ($row->product_qty <= $row->reorder_level)
                                        <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                                    @else
                                        <i class="fa-solid fa-check-to-slot text-success"></i>
                                    @endif
                                </td>
                                <td class="text-end"
                                    @can('update-product') onClick="update_price('{{ $row->product_id }}', '{{ $row->product_price }}', '{{ $row->product_description }}')" style="cursor: pointer" @endcan>
                                    {{ number_format($row->product_price, 2) }} <i
                                        class="fa-solid fa-pen-to-square text-primary"></i></td>
                                <td class="text-end">{{ $row->spv }}</td>
                                <td class="text-end"
                                    @can('update-reorder') onClick="update_reorder('{{ $row->product_id }}', '{{ $row->reorder_level }}', '{{ $row->product_description }}')" style="cursor: pointer" @endcan>
                                    {{ $row->reorder_level }} <i class="fa-solid fa-pen-to-square text-primary"></i>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
            <caption>{{ $data->links() }}</caption>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @error('code')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div class="input-group mb-2">
                        <span class="input-group-text w-25" id="code">Code</span>
                        <input type="text" class="form-control form-control-sm" wire:model.defer="code">
                    </div>
                    @error('product_description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div class="input-group mb-2">
                        <span class="input-group-text" id="new_desc">Product Name</span>
                        <input type="text" class="form-control form-control-sm"
                            wire:model.defer="product_description">
                    </div>
                    @error('category_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div class="input-group mb-2">
                        <span class="input-group-text w-25" id="new_desc">Category</span>
                        <select class="form-select form-select-sm" wire:model.defer="category_id">
                            <option value=""></option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('product_price')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div class="input-group mb-2">
                        <span class="input-group-text w-25" id="new_price">Price</span>
                        <input type="number" min="0" class="form-control form-control-sm"
                            wire:model.defer="product_price">
                    </div>
                    @error('spv')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div class="input-group mb-2">
                        <span class="input-group-text w-25" id="new_spv">SPV</span>
                        <input type="number" min="0" class="form-control form-control-sm"
                            wire:model.defer="spv">
                    </div>
                    <div class="input-group mb-2">
                        <span class="input-group-text" id="new_spv">Reorder Level</span>
                        <input type="number" min="0" class="form-control form-control-sm"
                            wire:model.defer="reorder_level">
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
        function update_price(product_id, product_price, product_desc) {
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

        function update_reorder(product_id, product_reorder_level, product_desc) {
            Swal.fire({
                title: '<h5> Update reorder level of ' + ' <strong>' + product_desc + '</strong></h5>',
                html: `<div class="input-group mb-2">
                            <span class="input-group-text" id="up">Reorder Level</span>
                            <input id="new_reorder" type="number" step="1" class="form-control form-control-sm">
                        </div>`,
                showCancelButton: true,
                confirmButtonText: `Confirm`,
                didOpen: () => {
                    const new_reorder = Swal.getHtmlContainer().querySelector('#new_reorder')
                    new_reorder.value = product_reorder_level;

                    new_reorder.addEventListener('input', () => {
                        @this.set('reorder_level', new_reorder.value);
                    })
                }
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    Livewire.emit('update_reorder', product_id)
                }
            });
        }
    </script>
@endpush
