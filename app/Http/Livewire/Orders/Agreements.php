<?php

namespace App\Http\Livewire\Orders;

use App\Models\Order;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class Agreements extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['view_order', 'clientSelected'];

    public $search;
    public $oa_number, $oa_count, $oa_date, $oa_client, $oa_address, $oa_contact, $oa_consultant, $oa_associate, $oa_presenter, $oa_team_builder, $oa_distributor;

    // Client selection
    public $client_id;
    public $client_search = '';
    public $filteredClients = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->oa_date = date('Y-m-d');
    }

    public function render()
    {
        $data = Order::where('oa_number', 'like', '%' . $this->search . '%')
            ->orWhere('oa_client', 'like', '%' . $this->search . '%')
            ->orWhere('oa_presenter', 'like', '%' . $this->search . '%')
            ->orWhere('oa_associate', 'like', '%' . $this->search . '%')
            ->orderByDesc('oa_id')
            ->paginate(20);

        // Filter clients for the dropdown
        if (!empty($this->client_search)) {
            $this->filteredClients = Client::where(function ($query) {
                $query->where('first_name', 'like', '%' . $this->client_search . '%')
                    ->orWhere('middle_name', 'like', '%' . $this->client_search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->client_search . '%')
                    ->orWhere('contact_number', 'like', '%' . $this->client_search . '%');
            })
                ->limit(10)
                ->get();
        } else {
            $this->filteredClients = [];
        }

        return view('livewire.orders.agreements', [
            'data' => $data,
            'clients' => $this->filteredClients,
        ]);
    }

    public function clientSelected($clientId)
    {
        $this->client_id = $clientId;
        $client = Client::find($clientId);

        if ($client) {
            $this->oa_client = $client->full_name;
            $this->oa_address = $client->address;
            $this->oa_contact = $client->contact_number;

            // Close the dropdown
            $this->client_search = '';
            $this->filteredClients = [];
        }
    }

    public function view_order(Order $order)
    {
        return redirect()->route('order.agreements.view', ['oa' => $order]);
    }

    public function save()
    {
        $date = date('Ymj');
        $this->oa_count = Order::max('oa_count') + 1;
        $this->oa_number = "OA" . $date . "-" . $this->oa_count;

        $validated_data = $this->validate([
            'oa_number' => 'required',
            'oa_count' => 'required',
            'oa_date' => 'required',
            'oa_client' => 'required',
            'oa_address' => 'nullable',
            'oa_contact' => 'nullable',
            'oa_consultant' => 'nullable',
            'oa_associate' => 'nullable',
            'oa_presenter' => 'nullable',
            'oa_team_builder' => 'nullable',
            'oa_distributor' => 'nullable',
            'client_id' => 'nullable|exists:clients,client_id',
        ]);

        $new = Order::create($validated_data);
        $this->reset_data();
        $this->dispatchBrowserEvent('success', 'Created new Order Agreement #' . $validated_data['oa_number']);
        $this->view_order($new);
    }

    public function reset_data()
    {
        $this->oa_number = null;
        $this->oa_count = null;
        $this->oa_date = date('Y-m-d');
        $this->oa_client = null;
        $this->oa_address = null;
        $this->oa_contact = null;
        $this->oa_consultant = null;
        $this->oa_associate = null;
        $this->oa_presenter = null;
        $this->oa_team_builder = null;
        $this->oa_distributor = null;
        $this->client_id = null;
        $this->client_search = '';
        $this->filteredClients = [];
    }
}
