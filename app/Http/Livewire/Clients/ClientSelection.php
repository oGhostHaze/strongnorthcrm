<?php

namespace App\Http\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientSelection extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'last_name';
    public $sortDirection = 'asc';

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
            ->paginate(10);

        return view('livewire.clients.client-selection', [
            'clients' => $clients
        ]);
    }

    public function selectClient($clientId)
    {
        // Emit to parent component
        $this->emitUp('clientSelected', $clientId);

        // Close the modal
        $this->dispatchBrowserEvent('close-modal', ['modalId' => 'modal-client-list']);
    }
}
