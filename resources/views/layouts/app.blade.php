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
    @livewireScripts
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <x-livewire-alert::scripts />
    @stack('scripts')
    <script>
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
        // $('body').on('contextmenu', function(e) {
        //     var top = e.pageY - 10;
        //     var left = e.pageX - 90;
        //     $("#context-menu").css({
        //         display: "block",
        //         top: top,
        //         left: left
        //     }).addClass("show");
        //     return false; //blocks default Webbrowser right click menu
        // }).on("click", function() {
        //     $("#context-menu").removeClass("show").hide();
        // });

        // $("#context-menu a").on("click", function() {
        //     $(this).parent().removeClass("show").hide();
        // });
    </script>
</body>

</html>
