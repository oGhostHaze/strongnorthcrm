<?php

namespace App\Http\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ClientList extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['refreshClients' => '$refresh', 'deleteClient'];

    public $search = '';
    public $sortField = 'last_name';
    public $sortDirection = 'asc';

    // Form fields
    public $first_name;
    public $middle_name;
    public $last_name;
    public $address;
    public $contact_number;
    public $tin_number;
    public $lifechanger_id;

    // For edit mode
    public $isEditing = false;
    public $editClientId;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function render()
    {
        $clients = Client::where(function ($query) {
            $query->where('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('contact_number', 'like', '%' . $this->search . '%')
                ->orWhere('tin_number', 'like', '%' . $this->search . '%');
        })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        return view('livewire.clients.client-list', [
            'clients' => $clients
        ]);
    }

    public function save()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'tin_number' => 'nullable|string|max:20',
            'lifechanger_id' => 'nullable|string|max:50',
        ]);

        if ($this->isEditing) {
            $client = Client::find($this->editClientId);
            $client->update([
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'address' => $this->address,
                'contact_number' => $this->contact_number,
                'tin_number' => $this->tin_number,
                'lifechanger_id' => $this->lifechanger_id,
            ]);

            $this->alert('success', 'Client updated successfully');
        } else {
            Client::create([
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'address' => $this->address,
                'contact_number' => $this->contact_number,
                'tin_number' => $this->tin_number,
                'lifechanger_id' => $this->lifechanger_id,
            ]);

            $this->alert('success', 'Client added successfully');
        }

        $this->resetForm();
    }

    public function editClient($clientId)
    {
        $this->isEditing = true;
        $this->editClientId = $clientId;

        $client = Client::find($clientId);
        $this->first_name = $client->first_name;
        $this->middle_name = $client->middle_name;
        $this->last_name = $client->last_name;
        $this->address = $client->address;
        $this->contact_number = $client->contact_number;
        $this->tin_number = $client->tin_number;
        $this->lifechanger_id = $client->lifechanger_id;
    }

    public function confirmDelete($clientId)
    {
        $this->alert('warning', 'Are you sure you want to delete this client?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'deleteClient',
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancel',
            'confirmButtonText' => 'Delete',
            'data' => [
                'client_id' => $clientId,
            ],
        ]);
    }

    public function deleteClient($data)
    {
        $clientId = $data['data']['client_id'];
        $client = Client::find($clientId);

        // Check if client has any orders
        if ($client->orders()->count() > 0) {
            $this->alert('error', 'Cannot delete client. This client has associated orders.');
            return;
        }

        $client->delete();
        $this->alert('success', 'Client deleted successfully');
    }

    public function resetForm()
    {
        $this->reset([
            'first_name',
            'middle_name',
            'last_name',
            'address',
            'contact_number',
            'tin_number',
            'lifechanger_id',
            'isEditing',
            'editClientId'
        ]);
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }
}
