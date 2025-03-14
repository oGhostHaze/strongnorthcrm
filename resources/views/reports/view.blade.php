@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $reportTitle }}</h5>
                <div>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa-solid fa-chevron-left mr-1"></i> Back to Generator
                    </a>
                    <button onclick="window.print()" class="btn btn-sm btn-primary ml-2">
                        <i class="fa-solid fa-print mr-1"></i> Print Report
                    </button>
                    <form action="{{ route('reports.generate') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="report_type" value="{{ $reportType }}">
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                        <input type="hidden" name="format" value="csv">
                        @if (isset($additionalCriteria))
                            @foreach ($additionalCriteria as $key => $value)
                                <input type="hidden" name="additional_criteria[{{ $key }}]"
                                    value="{{ $value }}">
                            @endforeach
                        @endif
                        <button type="submit" class="btn btn-sm btn-success ml-2">
                            <i class="fa-solid fa-file-csv mr-1"></i> Export to CSV
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <p class="mb-1"><strong>Date Range:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}
                        to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>

                    @if (isset($additionalCriteria) && !empty($additionalCriteria))
                        <p class="mb-0"><strong>Filters:</strong>
                            @foreach ($additionalCriteria as $key => $value)
                                @if (!empty($value))
                                    <span class="badge bg-info mr-2">{{ str_replace('_', ' ', ucfirst($key)) }}:
                                        {{ $value }}</span>
                                @endif
                            @endforeach
                        </p>
                    @endif
                </div>

                @if (empty($reportData))
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle mr-2"></i> No data found for the selected criteria.
                    </div>
                @else
                    <div class="table-responsive">
                        @if ($reportType == 'inventory')
                            @include('reports.partials.inventory_table')
                        @elseif($reportType == 'sales')
                            @include('reports.partials.sales_table')
                        @elseif($reportType == 'payments')
                            @include('reports.partials.payments_table')
                        @elseif($reportType == 'returns')
                            @include('reports.partials.returns_table')
                        @elseif($reportType == 'stockin')
                            @include('reports.partials.stockin_table')
                        @elseif($reportType == 'merchandise')
                            @include('reports.partials.merchandise_table')
                        @elseif($reportType == 'supplies')
                            @include('reports.partials.supplies_table')
                        @else
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        @foreach (array_keys($reportData[0]) as $column)
                                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reportData as $row)
                                        <tr>
                                            @foreach ($row as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div class="mt-4">
                        <p><strong>Total Records:</strong> {{ count($reportData) }}</p>

                        @if ($reportType == 'inventory')
                            <p><strong>Total Stock Value:</strong>
                                ₱{{ number_format(array_sum(array_column($reportData, 'stock_value')), 2) }}</p>
                        @elseif($reportType == 'sales')
                            @if (isset($additionalCriteria['group_by']))
                                <p><strong>Total Sales:</strong>
                                    ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</p>
                            @else
                                <p><strong>Total Sales:</strong>
                                    ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</p>
                            @endif
                        @elseif($reportType == 'payments')
                            <p><strong>Total Payments:</strong>
                                ₱{{ number_format(array_sum(array_column($reportData, 'amount')), 2) }}</p>
                        @endif
                    </div>
                @endif
            </div>
            <div class="card-footer text-muted">
                <small>Report generated on {{ date('F d, Y h:i A') }}</small>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style media="print">
        @page {
            size: landscape;
        }

        .no-print {
            display: none !important;
        }

        .card-header,
        .card-footer {
            background-color: white !important;
        }

        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
        }

        .card {
            border: none !important;
        }

        .table {
            width: 100% !important;
        }

        body {
            padding: 0 !important;
            margin: 0 !important;
        }
    </style>
@endpush
