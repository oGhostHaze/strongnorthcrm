<div>
    <div class="card">
        <div class="card-header">
            <h4>Client Masterlist</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search clients..." wire:model="search">
                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                            data-bs-target="#addClientModal">
                            <i class="fa fa-plus"></i> Add New Client
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('last_name')" style="cursor: pointer">
                                Name
                                @if ($sortField === 'last_name')
                                    <span class="float-end">
                                        <i class="fa fa-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    </span>
                                @endif
                            </th>
                            <th>Address</th>
                            <th>Contact Number</th>
                            <th>TIN Number</th>
                            <th>Lifechanger ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>{{ $client->last_name }}, {{ $client->first_name }} {{ $client->middle_name }}</td>
                                <td>{{ $client->address }}</td>
                                <td>{{ $client->contact_number }}</td>
                                <td>{{ $client->tin_number }}</td>
                                <td>{{ $client->lifechanger_id }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary"
                                        wire:click="editClient({{ $client->client_id }})" data-bs-toggle="modal"
                                        data-bs-target="#addClientModal">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        wire:click="confirmDelete({{ $client->client_id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No clients found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $clients->links() }}
            </div>
        </div>
    </div>

    <!-- Add/Edit Client Modal -->
    <div wire:ignore.self class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">
                        {{ $isEditing ? 'Edit Client' : 'Add New Client' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="last_name" class="form-label">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                    id="last_name" wire:model="last_name">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="first_name" class="form-label">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                    id="first_name" wire:model="first_name">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                                    id="middle_name" wire:model="middle_name">
                                @error('middle_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" wire:model="address"
                                rows="3"></textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                                    id="contact_number" wire:model="contact_number">
                                @error('contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="tin_number" class="form-label">TIN Number</label>
                                <input type="text" class="form-control @error('tin_number') is-invalid @enderror"
                                    id="tin_number" wire:model="tin_number">
                                @error('tin_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="lifechanger_id" class="form-label">Lifechanger ID</label>
                                <input type="text"
                                    class="form-control @error('lifechanger_id') is-invalid @enderror"
                                    id="lifechanger_id" wire:model="lifechanger_id">
                                @error('lifechanger_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        wire:click="cancelEdit">Cancel</button>
                    <button type="button" class="btn btn-primary"
                        wire:click="save">{{ $isEditing ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
