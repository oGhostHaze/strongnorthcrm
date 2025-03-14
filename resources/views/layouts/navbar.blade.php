<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/"><i class="fa-solid fa-home"></i>
                        Home</a>
                </li>
                @auth
                    @can('view-products')
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fa-solid fa-box-open"></i> Products
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('product.list') }}">
                                    <i class="fa-solid fa-box-open"></i> Product List
                                </a>
                                <a class="dropdown-item" href="{{ route('set.list') }}">
                                    <i class="fa-solid fa-box"></i> Set List
                                </a>
                                @can('view-stockin')
                                    <a class="dropdown-item" href="{{ route('product.stockin') }}">
                                        <i class="fa-solid fa-dolly"></i> Stock-in
                                    </a>
                                @endcan
                            </div>
                        </li>
                    @endcan

                    @can('view-orders')
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fa-regular fa-copy"></i> Orders
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('order.agreements') }}">
                                    <i class="fa-solid fa-file-lines"></i> Order Agreements
                                </a>
                                <a class="dropdown-item" href="{{ route('order.agreements.cs') }}">
                                    <i class="fa-solid fa-file-import"></i> OA from Shows
                                </a>
                                <a class="dropdown-item" href="{{ route('order.delivery.list') }}">
                                    <i class="fa-solid fa-list"></i> Delivery Receipts
                                </a>
                                <a class="dropdown-item" href="{{ route('order.returns.list') }}">
                                    <i class="fa-solid fa-rotate-left"></i> Return Slips
                                </a>
                            </div>
                        </li>
                    @endcan

                    @can('view-servicing')
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fa-brands fa-servicestack"></i> Servicing
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('servicing.list') }}">
                                    <i class="fa-regular fa-file-lines"></i> List
                                </a>
                                <a class="dropdown-item" href="{{ route('order.delivery.list') }}">
                                    <i class="fa-solid fa-list"></i> Inventory
                                </a>
                            </div>
                        </li>
                    @endcan

                    @can('view-merchandise')
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fa-brands fa-shopify"></i> Merch. Mgmt.
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('merch.items') }}">
                                    <i class="fa-regular fa-file-lines"></i> Merch List
                                </a>
                                <a class="dropdown-item" href="{{ route('merch.orders.list') }}">
                                    <i class="fa-solid fa-list"></i> Merch Orders
                                </a>
                                <a class="dropdown-item" href="{{ route('merch.order.stockin') }}">
                                    <i class="fa-solid fa-truck-ramp-box"></i> Merch Stock in
                                </a>
                            </div>
                        </li>
                    @endcan

                    @can('view-material')
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fa-solid fa-sitemap"></i> Material Mgmt.
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('supp.list') }}">
                                    <i class="fa-solid fa-list"></i> Supply List
                                </a>
                                <a class="dropdown-item" href="{{ route('supp.disposed') }}">
                                    <i class="fa-brands fa-unsplash"></i> Disposed Supplies
                                </a>
                            </div>
                        </li>
                    @endcan
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa-regular fa-file"></i> Reports
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('reports.index') }}">
                                Report Generator
                            </a>
                            <a class="dropdown-item" href="{{ route('inventory.all') }}">
                                Product Inventory
                            </a>
                            <a class="dropdown-item" href="{{ route('inventory.per-dr') }}">
                                Per Delivery Receipt
                            </a>
                            <a class="dropdown-item" href="{{ route('inventory.merch.all') }}">
                                Merchandise Inventory
                            </a>
                            <a class="dropdown-item" href="{{ route('inventory.supply.all') }}">
                                Office Supply Inventory
                            </a>
                            <a class="dropdown-item" href="{{ route('rep.payments') }}">
                                Order Payments
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa-solid fa-book"></i> Reference Tables
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('supp.category') }}">
                                <i class="fa-solid fa-draw-polygon"></i> Categories
                            </a>
                            <a class="dropdown-item" href="{{ route('supp.location') }}">
                                <i class="fa-solid fa-location-dot"></i> Locations
                            </a>
                            <a class="dropdown-item" href="{{ route('ref.uom') }}">
                                <i class="fa-solid fa-ruler"></i> Unit of Measurement
                            </a>
                            <a class="dropdown-item" href="{{ route('ref.mop') }}">
                                <i class="fa-solid fa-credit-card"></i> Mode of Payments
                            </a>
                        </div>
                    </li>
                    @can('view-employees')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('employee.list') }}"><i class="fa-solid fa-users"></i>
                                Employees</a>
                        </li>
                    @endcan
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa-solid fa-id-badge"></i> {{ Auth::user()->email }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @can('access-commissions')
                                <a class="dropdown-item" href="https://comms.strongnorth-saladmaster.com/">
                                    {{ __('Commissions') }}
                                </a>
                            @endcan
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
