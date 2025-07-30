@php
    $statusClasses = [
        1 => 'bg-primary',     // New
        2 => 'bg-info',        // Downloaded
        3 => 'bg-warning',     // Delivery
        4 => 'bg-success',     // Invoiced
        5 => 'bg-danger',      // On Hold
    ];

    $statusNames = [
        1 => 'New',
        2 => 'Downloaded',
        3 => 'Delivery',
        4 => 'Invoiced',
        5 => 'On Hold',
    ];

    $badgeClass = $statusClasses[$status] ?? 'bg-secondary';
    $statusName = $statusNames[$status] ?? 'Unknown';
@endphp

<span class="badge {{ $badgeClass }} {{ $class ?? '' }}">
    @if($showIcon ?? false)
        @switch($status)
            @case(1)
                <i class="fas fa-plus-circle me-1"></i>
                @break
            @case(2)
                <i class="fas fa-download me-1"></i>
                @break
            @case(3)
                <i class="fas fa-truck me-1"></i>
                @break
            @case(4)
                <i class="fas fa-check-circle me-1"></i>
                @break
            @case(5)
                <i class="fas fa-pause-circle me-1"></i>
                @break
        @endswitch
    @endif
    {{ $statusName }}
</span>
