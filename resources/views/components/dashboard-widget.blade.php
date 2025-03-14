@props([
    'title' => 'Widget Title',
    'value' => '0',
    'icon' => 'fa-chart-line',
    'color' => 'primary',
    'route' => null,
])

<div {{ $attributes->merge(['class' => "card border-left-$color shadow h-100 py-2 dashboard-summary-card"]) }}>
    <div class="card-body">
        <div class="row no-gutters align-items-center">
            <div class="col mr-2">
                <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                    {{ $title }}
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $value }}</div>
                @if (isset($subtext))
                    <div class="text-xs text-muted mt-1">{{ $subtext }}</div>
                @endif
            </div>
            <div class="col-auto">
                <i class="fa-solid {{ $icon }} fa-2x text-gray-300"></i>
            </div>
        </div>
        @if ($route)
            <div class="row mt-2">
                <div class="col-12">
                    <a href="{{ $route }}" class="btn btn-sm btn-{{ $color }} btn-block">View Details</a>
                </div>
            </div>
        @endif
    </div>
</div>
