@if($paginator->hasPages())
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">

    {{-- Texto de resultados --}}
    <p class="small text-dark mb-0">
        Mostrando
        @if($paginator->firstItem())
            <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
            a
            <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
        @else
            <span class="fw-semibold">{{ $paginator->count() }}</span>
        @endif
        de
        <span class="fw-semibold">{{ $paginator->total() }}</span>
        resultados
    </p>

    {{-- Navegación --}}
    <nav>
        <ul class="pagination pagination-sm mb-0">

            {{-- Anterior --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if($paginator->onFirstPage())
                    <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                @else
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                @endif
            </li>

            {{-- Números de página --}}
            @php
                $start = max(1, $paginator->currentPage() - 2);
                $end   = min($paginator->lastPage(), $paginator->currentPage() + 2);
            @endphp

            @if($start > 1)
                <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
                @if($start > 2)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif
            @endif

            @for($page = $start; $page <= $end; $page++)
                <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endfor

            @if($end < $paginator->lastPage())
                @if($end < $paginator->lastPage() - 1)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif
                <li class="page-item"><a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a></li>
            @endif

            {{-- Siguiente --}}
            <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                @if($paginator->hasMorePages())
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                @endif
            </li>

        </ul>
    </nav>
</div>
@endif