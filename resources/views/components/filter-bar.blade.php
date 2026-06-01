<div class="card mb-3">
    <div class="card-body">
        <form action="{{ $action }}" method="GET">
            <div class="row g-2 align-items-end">
                {{ $slot }}

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                </div>
                <div class="col-auto">
                    <a href="{{ $action }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>