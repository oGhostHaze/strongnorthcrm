<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Select2 Additional Styling -->
    <style>
        section {
            margin-bottom: 0.5in;
        }

        @media print {
            section {
                page-break-after: always;
            }
        }

        /* Enhanced styling for Select2 dropdowns */
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
        }

        .product-description {
            font-weight: 500;
            max-width: 80%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Make the dropdown wider for better visibility */
        .select2-dropdown {
            min-width: 350px !important;
        }

        /* Style for quantity badge */
        .badge.bg-secondary {
            font-size: 0.75rem;
            font-weight: normal;
        }
    </style>
    @stack('styles')

    @livewireStyles
</head>

<body>
    <div id="app">
        <font face="Raleway">
            @include('layouts.navbar')

            <main class="py-4">
                @yield('content')
                {{ $slot ?? '' }}
            </main>

            <div class="dropdown-menu dropdown-menu-sm" id="context-menu">
                <a class="dropdown-item" onclick="history.back()"><i class="fa-solid fa-arrow-left"></i> Back</a>
                <a class="dropdown-item" href="{{ route('home') }}"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                <a class="dropdown-item" onclick="location.reload();"><i class="fa-solid fa-rotate-right"></i>
                    Reload</a>
            </div>
        </font>
    </div>

    <!-- Place jQuery first, before Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>

    <!-- Then Bootstrap and other libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Livewire scripts -->
    @livewireScripts

    <!-- Livewire Alert scripts -->
    <x-livewire-alert::scripts />

    <!-- Your custom scripts -->
    @stack('scripts')
    <script>
        // Initialize tooltips properly on page load and after Livewire updates
        document.addEventListener('livewire:load', function() {
            initTooltips();

            // Handle Livewire updates
            Livewire.hook('message.processed', (message, component) => {
                // Destroy all existing tooltips first
                destroyTooltips();

                // Reinitialize tooltips
                setTimeout(() => {
                    initTooltips();
                }, 100);
            });
        });

        // Function to initialize tooltips
        function initTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(el => {
                new bootstrap.Tooltip(el, {
                    trigger: 'hover',
                    placement: 'top',
                    container: 'body',
                    boundary: 'window'
                });
            });
        }

        // Function to destroy tooltips
        function destroyTooltips() {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(element => {
                const tooltip = bootstrap.Tooltip.getInstance(element);
                if (tooltip) {
                    tooltip.dispose();
                }
            });
        }

        window.addEventListener('success', event => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: event.detail,
                showConfirmButton: false,
                timer: 2000
            });
        });

        window.addEventListener('error', event => {
            Swal.fire({
                icon: 'error',
                title: 'Something went wrong!',
                text: event.detail,
                showConfirmButton: false,
            });
        });

        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
    </script>
</body>

</html>
