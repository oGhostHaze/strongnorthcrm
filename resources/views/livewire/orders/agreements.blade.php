<div>
    <div class="card">
        <div class="card-header">
            <h4>Order Agreements</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control"
                            placeholder="Search by oa number, client, presenter, or associate" wire:model="search">
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#modal-new-OA">
                            <i class="fa fa-plus"></i> New Order Agreement
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>OA #</th>
                            <th>Client</th>
                            <th>Contact #</th>
                            <th>Lifechanger</th>
                            <th>Partner</th>
                            <th>Presenter</th>
                            <th>To Follow</th>
                            <th>Released</th>
                            <th>DR Count</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            @php
                                $released = 0;
                                $to_follow = 0;
                            @endphp
                            <tr wire:click="$emit('view_order', {{ $row }})"
                                style="cursor: pointer; font-size: 0.9rem;">
                                <td>{{ $row->oa_date }}</td>
                                <td>{{ $row->oa_number }}</td>
                                <td>{{ $row->oa_client }}</td>
                                <td>{{ $row->oa_contact }}</td>
                                <td>{{ $row->oa_consultant }}</td>
                                <td>{{ $row->oa_associate }}</td>
                                <td>{{ $row->oa_presenter }}</td>
                                <td>{{ $to_follow += $row->items()->sum('item_qty') + $row->gifts()->sum('item_qty') }}
                                </td>
                                <td>{{ $released += $row->items()->sum('released') + $row->gifts()->sum('released') }}
                                </td>
                                <td>{{ $row->drs()->count() }}</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar @if ($row->percentage() >= 30) bg-success @else bg-danger @endif"
                                            role="progressbar" style="width: {{ $row->percentage() }}%"
                                            aria-valuenow="{{ $row->percentage() }}" aria-valuemin="0"
                                            aria-valuemax="100">
                                            {{ $row->percentage() }}%</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No records found...</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $data->links() }}
            </div>
        </div>
    </div>

    <!-- New Order Agreement Modal -->
    <div wire:ignore.self class="modal fade" id="modal-new-OA" tabindex="-1" role="dialog"
        aria-labelledby="modal-new-OALabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-new-OALabel">Create Order Agreement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="form-group mb-2">
                            <label for="oa_date">Date</label>
                            <input type="date" class="form-control" id="oa_date" wire:model="oa_date">
                            @error('oa_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Client Selection Section (Added) -->
                        <div class="form-group mb-3">
                            <label>Select from Client Masterlist (Optional)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="client_search" wire:model="client_search"
                                    placeholder="Search client by name or contact">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#modal-client-list">
                                    <i class="fa fa-users"></i> Browse
                                </button>
                            </div>

                            @if (count($filteredClients) > 0)
                                <div class="list-group mt-1">
                                    @foreach ($filteredClients as $client)
                                        <button type="button" class="list-group-item list-group-item-action"
                                            wire:click="clientSelected({{ $client->client_id }})">
                                            {{ $client->last_name }}, {{ $client->first_name }}
                                            {{ $client->middle_name }}
                                            - {{ $client->contact_number }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="form-group mb-2">
                            <label for="oa_client">Client</label>
                            <input type="text" class="form-control" id="oa_client" wire:model="oa_client">
                            @error('oa_client')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-2">
                            <label for="oa_address">Address</label>
                            <textarea class="form-control" id="oa_address" wire:model="oa_address" rows="2"></textarea>
                            @error('oa_address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-2">
                            <label for="oa_contact">Contact</label>
                            <input type="text" class="form-control" id="oa_contact" wire:model="oa_contact">
                            @error('oa_contact')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="input-group mb-2">
                            <span class="input-group-text" id="oa_consultant">Consultant</span>
                            <input type="text" class="form-control form-control-sm" wire:model.defer="oa_consultant">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                @error('oa_associate')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="oa_associate">Associate</span>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.defer="oa_associate">
                                </div>
                            </div>
                            <div class="col-6">
                                @error('oa_presenter')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="oa_presenter">Presenter</span>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.defer="oa_presenter">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                @error('oa_team_builder')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="oa_team_builder">Team Builder</span>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.defer="oa_team_builder">
                                </div>
                            </div>
                            <div class="col-6">
                                @error('oa_distributor')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="oa_distributor">Distributor</span>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.defer="oa_distributor">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="save">Save Order Agreement</button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="modal-client-list" tabindex="-1" role="dialog"
        aria-labelledby="modal-client-listLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-client-listLabel">Client Selection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- This section will include the client list component -->
                    @livewire('clients.client-selection')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
