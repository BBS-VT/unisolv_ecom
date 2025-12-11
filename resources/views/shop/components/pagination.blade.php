@if ($paginator->hasPages())
    @php
        // Calculate the page range to display
        $start = $paginator->currentPage() - 2; // Show 2 pages before current
        $end = $paginator->currentPage() + 2; // Show 2 pages after current

        if ($start < 1) {
            $start = 1;
            $end = min(5, $paginator->lastPage());
        }

        if ($end > $paginator->lastPage()) {
            $end = $paginator->lastPage();
            $start = max(1, $end - 4);
        }
    @endphp

    <nav aria-label="Page navigation">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="bi bi-chevron-left"></i>
                        <span class="d-none d-sm-inline">Previous</span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left"></i>
                        <span class="d-none d-sm-inline">Previous</span>
                    </a>
                </li>
            @endif

            {{-- First Page Link (if not in range) --}}
            @if($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if($start > 2)
                    <li class="page-item disabled">
                        <span class="page-link">…</span>
                    </li>
                @endif
            @endif

            {{-- Page Links --}}
            @for($i = $start; $i <= $end; $i++)
                @if ($i == $paginator->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $i }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            {{-- Last Page Link (if not in range) --}}
            @if($end < $paginator->lastPage())
                @if($end < $paginator->lastPage() - 1)
                    <li class="page-item disabled">
                        <span class="page-link">…</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">
                        {{ $paginator->lastPage() }}
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <span class="d-none d-sm-inline">Next</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <span class="d-none d-sm-inline">Next</span>
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif

@once
    @push('styles')
        <style>
            .pagination {
                gap: 0.25rem;
                margin: 0;
            }

            .page-link {
                color: #333;
                background-color: #fff;
                border: 1px solid #ddd;
                padding: 0.5rem 0.75rem;
                font-size: 0.95rem;
                transition: all 0.2s;
                min-width: 40px;
                text-align: center;
            }

            .page-link:hover {
                background-color: #f5f5f5;
                border-color: #667eea;
                color: #667eea;
                text-decoration: none;
            }

            .page-link:focus {
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
                outline: none;
            }

            .page-item.active .page-link {

                border-color: #667eea;
                color: #fff;
                font-weight: 600;
            }

            .page-item.disabled .page-link {
                color: #999;
                background-color: #f8f9fa;
                border-color: #ddd;
                cursor: not-allowed;
                opacity: 0.6;
            }

            /* Mobile responsive */
            @media (max-width: 576px) {
                .pagination {
                    flex-wrap: wrap;
                    justify-content: center;
                }

                .page-link {
                    padding: 0.4rem 0.6rem;
                    font-size: 0.875rem;
                    min-width: 35px;
                }
            }
        </style>
    @endpush
@endonce
