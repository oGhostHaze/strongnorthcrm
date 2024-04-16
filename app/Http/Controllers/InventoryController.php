<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryDate;
use App\Models\InventoryItem;
use App\Models\MerchandiseDeliveryItem;
use App\Models\MerchandiseInventoryDate;
use App\Models\MerchandiseInventoryItem;
use App\Models\SupplyInventoryDate;
use App\Models\SupplyInventoryItem;

class InventoryController extends Controller
{
    public static function record_merchandise_inventory($type, $qty, $product_id)
    {
        $inventory_date = MerchandiseInventoryDate::firstOrCreate(['date' => date('Y-m-d')]);
        $inventory_item = MerchandiseInventoryItem::firstOrCreate([
            'date' => $inventory_date->id,
            'product_id' => $product_id
        ]);

        switch($type){
            case 'stockin':
                $inventory_item->total_delivered += $qty;
                break;

            case 'delivery':
                $inventory_item->total_released += $qty;
                break;

            case 'return':
                $inventory_item->total_returned += $qty;
                break;
        }

        $inventory_item->save();

        return;
    }

    public static function record_supply_inventory($type, $qty, $item_id)
    {
        $inventory_date = SupplyInventoryDate::firstOrCreate(['date' => date('Y-m-d')]);
        $inventory_item = SupplyInventoryItem::firstOrCreate([
            'date' => $inventory_date->id,
            'item_id' => $item_id
        ]);

        switch($type){
            case 'add':
                $inventory_item->added += $qty;
                break;

            case 'dispose':
                $inventory_item->disposed += $qty;
                break;
        }

        $inventory_item->save();

        return;
    }
}
