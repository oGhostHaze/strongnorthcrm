<?php

namespace App\Console\Commands;

use App\Models\SupplyInventoryDate;
use App\Models\SupplyInventoryItem;
use App\Models\SupplyItem;
use Illuminate\Console\Command;

class InventorySupplies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:supplies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Office Supplies inventory beginning balance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $inventory_date = SupplyInventoryDate::where('date', date('Y-m-d'))->first();
        if(!$inventory_date){
            $inventory_date = SupplyInventoryDate::create(['date' => date('Y-m-d')]);
            $supplies = SupplyItem::where('qty', '<>', '0')->get();
            foreach($supplies as $supp){
                // $inventory_date = InventoryDate::firstOrCreate(['date' => date('Y-m-d')]);
                $inventory_item = SupplyInventoryItem::firstOrCreate([
                    'date' => $inventory_date->id,
                    'item_id' => $supp->id
                ]);
                $inventory_item->beginning_balance += $supp->qty;
                $inventory_item->save();
            }
            info('Inventory beginning balance updated');
        }else{
            info('Inventory beginning balance has already been set. This command will run again at midnight');
        }
        return 0;
    }
}
