<?php

namespace App\Console\Commands;

use App\Models\MerchandiseInventoryDate;
use App\Models\MerchandiseInventoryItem;
use App\Models\MerchandiseItem;
use Illuminate\Console\Command;

class InventoryMerchandise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:merchandise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Merchandise inventory beginning balance';

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
        $inventory_date = MerchandiseInventoryDate::where('date', date('Y-m-d'))->first();
        if(!$inventory_date){
            $inventory_date = MerchandiseInventoryDate::create(['date' => date('Y-m-d')]);
            $merchandise = MerchandiseItem::where('qty', '<>', '0')->get();
            foreach($merchandise as $merch){
                // $inventory_date = InventoryDate::firstOrCreate(['date' => date('Y-m-d')]);
                $inventory_item = MerchandiseInventoryItem::firstOrCreate([
                    'date' => $inventory_date->id,
                    'product_id' => $merch->id
                ]);
                $inventory_item->beginning_balance += $merch->qty;
                $inventory_item->save();
            }
            info('Inventory beginning balance updated');
        }else{
            info('Inventory beginning balance has already been set. This command will run again at midnight');
        }
        return 0;
    }
}
