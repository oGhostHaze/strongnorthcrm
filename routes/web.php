<?php

use App\Http\Controllers\OrderController;
use App\Http\Livewire\Home;
use App\Http\Livewire\Sets\Setlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Inventories\All;
use App\Http\Livewire\Servicing\SrList;
use App\Http\Livewire\Servicing\SrView;
use App\Http\Livewire\Sets\Composition;
use App\Http\Livewire\Inventories\PerDr;
use App\Http\Livewire\Orders\Agreements;
use App\Http\Livewire\Orders\AgreementView;
use App\Http\Livewire\Products\ProductList;
use App\Http\Livewire\Products\StockinList;
use App\Http\Livewire\Inventories\SupplyAll;
use App\Http\Livewire\Employees\EmployeeList;
use App\Http\Livewire\Merchandise\MerchItems;
use App\Http\Livewire\Deliveries\DeliveryList;
use App\Http\Livewire\Deliveries\DeliveryView;
use App\Http\Livewire\Merchandise\MerchOrders;
use App\Http\Livewire\Supplies\SupplyItemList;
use App\Http\Livewire\Supplies\SupplyLocation;
use App\Http\Livewire\CookingShows\ViewCsOrder;
use App\Http\Livewire\Merchandise\MerchRsnView;
use App\Http\Livewire\Merchandise\MerchStockIn;
use League\CommonMark\Delimiter\DelimiterStack;
use App\Http\Livewire\Orders\Returns\Listreturn;
use App\Http\Livewire\Orders\Returns\Viewreturn;
use App\Http\Livewire\Supplies\SupplyCategories;
use App\Http\Livewire\CookingShows\PendingOrders;
use App\Http\Livewire\Inventories\MerchandiseAll;
use App\Http\Livewire\References\MeasurementUnit;
use App\Http\Livewire\Merchandise\MerchOrdersView;
use App\Http\Livewire\Supplies\SupplyDisposedItems;
use App\Http\Livewire\Merchandise\MerchDeliveryView;
use App\Http\Livewire\Products\StockinReportFiltered;
use App\Http\Livewire\References\ManageModeOfPayment;
use App\Http\Livewire\Reports\PaymentReport;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('home');
    });
    Route::get('/home', Home::class)->name('home');

    Route::get('/product/list', ProductList::class)->name('product.list');
    Route::get('/product/sets/list', Setlist::class)->name('set.list');
    Route::get('/product/sets/view/{set}', Composition::class)->name('set.view');

    Route::get('/product/stockin', StockinList::class)->name('product.stockin');
    Route::get('/product/stockin/report', StockinReportFiltered::class)->name('product.stockin.filtered');

    Route::get('/order/agreements', Agreements::class)->name('order.agreements');
    Route::get('/order/agreements/cooking-shows', PendingOrders::class)->name('order.agreements.cs');
    Route::get('/order/agreements/view/{oa}', AgreementView::class)->name('order.agreements.view');
    Route::get('/order/agreements/view/{oa_no}/print', [OrderController::class, 'view'])->name('order.agreements.view.print');
    Route::get('/order/agreements/returns/view/{rsn}', Viewreturn::class)->name('order.returns.view');
    Route::get('/order/returns/list', Listreturn::class)->name('order.returns.list');

    Route::get('/order/delivery/view/{transno}', DeliveryView::class)->name('order.delivery.view');

    Route::get('/order/delivery', DeliveryList::class)->name('order.delivery.list');

    Route::get('/order-cs/view/{oa_id}', ViewCsOrder::class)->name('oa.view');

    Route::get('/employees/list', EmployeeList::class)->name('employee.list');


    Route::prefix('/inventory')->name('inventory.')->group(function () {
        Route::get('/all', All::class)->name('all');
        Route::get('/merchandise/all', MerchandiseAll::class)->name('merch.all');
        Route::get('/per-delivery-items', PerDr::class)->name('per-dr');
        Route::get('/supply.all', SupplyAll::class)->name('supply.all');
    });

    // Route::get('/inventory/all', All::class)->name('inventory.all');
    // Route::get('/inventory/merchandise/all', MerchandiseAll::class)->name('inventory.merch.all');
    // Route::get('/inventory/per-delivery-items', PerDr::class)->name('inventory.per-dr');

    Route::get('/servicing/list', SrList::class)->name('servicing.list');

    Route::get('/servicing/view/{servicing}', SrView::class)->name('servicing.view');

    Route::prefix('/merchandise')->name('merch.')->group(function () {
        Route::get('/items', MerchItems::class)->name('items');
        Route::get('/orders/list', MerchOrders::class)->name('orders.list');
        Route::get('/order/view/{merch}', MerchOrdersView::class)->name('order.view');
        Route::get('/order/stockin', MerchStockIn::class)->name('order.stockin');
        Route::get('/delivery/view/{transno}', MerchDeliveryView::class)->name('delivery.view');
        Route::get('/order/returns/view/{rsn}', MerchRsnView::class)->name('rsn.view');
    });

    Route::prefix('/supplies')->name('supp.')->group(function () {
        Route::get('/categories', SupplyCategories::class)->name('category');
        Route::get('/locations', SupplyLocation::class)->name('location');
        Route::get('/items', SupplyItemList::class)->name('list');
        Route::get('/disposed', SupplyDisposedItems::class)->name('disposed');
    });

    Route::prefix('/references')->name('ref.')->group(function () {
        Route::get('/unit-of-measurement', MeasurementUnit::class)->name('uom');
        Route::get('/mode-of-payments', ManageModeOfPayment::class)->name('mop');
    });

    Route::prefix('/reports')->name('rep.')->group(function () {
        Route::get('/order-payments', PaymentReport::class)->name('payments');
    });
});
