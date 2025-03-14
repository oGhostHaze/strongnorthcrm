@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Saladmaster Inventory Management Dashboard</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>Welcome to the Saladmaster Inventory Management System. Use the dashboard below to
                                    monitor key metrics and access essential functions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @livewire('home')
    </div>
@endsection

@push('styles')
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any dashboard-specific JavaScript here
            console.log('Dashboard loaded');
        });
    </script>
@endpush
