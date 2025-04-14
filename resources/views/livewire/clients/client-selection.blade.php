<div>
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
                        <td>
                            <button class="btn btn-sm btn-success" wire:click="selectClient({{ $client->client_id }})">
                                <i class="fa fa-check"></i> Select
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No clients found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $clients->links() }}
    </div>

    <script>
        window.addEventListener('close-modal', event => {
            $('#' + event.detail.modalId).modal('hide');
        });
    </script>
</div>
