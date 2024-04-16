<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <span class="text-uppercase fw-bolder">{{$set->set_name}}</span>
                <div class="d-flex">
                    <div class="">
                        @can('add-set-composition')
                        <a class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">Add Item</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th width="10%">Product ID</th>
                            <th width="10%">Quantity</th>
                            <th>Product Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($set->compositions()->get() as $row)
                            <tr>
                                <td>{{$row->product_id}}</td>
                                <td>{{$row->qty}}</td>
                                <td>{{$row->product->product_description}}</td>
                            </tr>
                        @empty
                        <tr class="table-danger">
                            <td colspan="2" class="text-center fw-bold">No record found!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Add Item Modal --}}
        <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModal" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="addItemModal">Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" wire:ignore>
                        <label for="product_id" class="form-label">Product</label><br>
                        <select class="form-control" id="product_id" wire:model.defer="product_id" style="width: 100%;">
                                <option value=""></option>
                            @foreach ($products as $product)
                                <option value="{{$product->product_id}}">{{$product->product_description}}</option>
                            @endforeach
                        </select>
                      </div>
                    <div class="mb-3">
                        <label for="qty" class="form-label">Qty</label><br>
                        <input type="number" step="1" class="form-control" id="qty" wire:model.defer="qty">
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
</div>
