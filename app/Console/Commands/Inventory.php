<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\InventoryDate;
use App\Models\InventoryItem;
use Illuminate\Console\Command;

class Inventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:begin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set inventory beginning balance';

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
        $inventory_date = InventoryDate::where('inv_date', date('Y-m-d'))->first();
        if(!$inventory_date){
            $inventory_date = InventoryDate::create(['inv_date' => date('Y-m-d')]);
            $products = Product::where('product_qty', '<>', '0')->get();
            foreach($products as $product){
                // $inventory_date = InventoryDate::firstOrCreate(['inv_date' => date('Y-m-d')]);
                $inventory_item = InventoryItem::firstOrCreate([
                    'inventory_date_id' => $inventory_date->id,
                    'product_id' => $product->product_id
                ]);
                $inventory_item->beginning_balance += $product->product_qty;
                $inventory_item->save();
            }
            info('Inventory beginning balance updated');
        }else{
            info('Inventory beginning balance has already been set. This command will run again at midnight');
        }
        return 0;
    }
}
