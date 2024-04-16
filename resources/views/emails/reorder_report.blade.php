<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <x-livewire-alert::scripts />
    @livewireStyles
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        section {
            margin-bottom: 0.5in;
        }

        @media print {
            section {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <font face="Raleway">
            <main class="py-4">
                <h2 class="text-uppercase text-xl fw-bolder mb-3">Products on or below the Safety Level</h2>
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>Code</th>
                                <th>Description</th>
                                <th class="text-end">Available QTY</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Reorder Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $row)
                                <tr>
                                    <td>{{ $row->code }}</td>
                                    <td>{{ $row->product_description }}</td>
                                    <td class="text-end">
                                        {{ $row->product_qty }}
                                        @if ($row->product_qty <= $row->reorder_level)
                                            <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                                        @else
                                            <i class="fa-solid fa-check-to-slot text-success"></i>
                                        @endif
                                    </td>
                                    <td class="text-end"> {{ number_format($row->product_price, 2) }}</td>
                                    <td class="text-end"> {{ $row->reorder_level }} </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </main>
        </font>
    </div>
</body>

</html>
