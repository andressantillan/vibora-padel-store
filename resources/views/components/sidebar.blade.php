<div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px; min-height: 100vh;">

    {{-- Logo --}}
    <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-3 text-white text-decoration-none">
        <span class="fs-4 fw-bold">Panel de Control</span>
    </a>
    <hr class="border-secondary">

    {{-- Navegación --}}
    <ul class="nav nav-pills flex-column gap-1 mb-auto">

        {{-- Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link text-white sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        {{-- ===== CATÁLOGO ===== --}}
        @can('catalog.view')
            {{-- Marcas --}}
            <li class="nav-item">
                @php $brandsActive = request()->routeIs('admin.brands.*'); @endphp
                <a href="#menu-brands"
                   class="nav-link text-white sidebar-link d-flex justify-content-between align-items-center {{ $brandsActive ? 'active' : '' }}"
                   data-bs-toggle="collapse" aria-expanded="{{ $brandsActive ? 'true' : 'false' }}">
                    <span><i class="bi bi-tags me-2"></i> Marcas</span>
                    <i class="bi bi-chevron-down sidebar-chevron {{ $brandsActive ? 'rotated' : '' }}"></i>
                </a>
                <div class="collapse {{ $brandsActive ? 'show' : '' }}" id="menu-brands">
                    <ul class="nav flex-column ms-3 mt-1 gap-1 border-start border-secondary ps-2">
                        <li class="nav-item">
                            <a href="{{ route('admin.brands.index') }}"
                               class="nav-link text-white sidebar-link {{ request()->routeIs('admin.brands.index') ? 'active' : '' }}">
                                <i class="bi bi-list me-2"></i> Listar marcas
                            </a>
                        </li>
                        @can('catalog.manage')
                        <li class="nav-item">
                            <a href="{{ route('admin.brands.create') }}"
                               class="nav-link text-white sidebar-link {{ request()->routeIs('admin.brands.create') ? 'active' : '' }}">
                                <i class="bi bi-plus-circle me-2"></i> Nueva marca
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>

            {{-- Categorías --}}
            <li class="nav-item">
                @php $categoriesActive = request()->routeIs('admin.categories.*'); @endphp
                <a href="#menu-categories"
                   class="nav-link text-white sidebar-link d-flex justify-content-between align-items-center {{ $categoriesActive ? 'active' : '' }}"
                   data-bs-toggle="collapse" aria-expanded="{{ $categoriesActive ? 'true' : 'false' }}">
                    <span><i class="bi bi-folder2 me-2"></i> Categorías</span>
                    <i class="bi bi-chevron-down sidebar-chevron {{ $categoriesActive ? 'rotated' : '' }}"></i>
                </a>
                <div class="collapse {{ $categoriesActive ? 'show' : '' }}" id="menu-categories">
                    <ul class="nav flex-column ms-3 mt-1 gap-1 border-start border-secondary ps-2">
                        <li class="nav-item">
                            <a href="{{ route('admin.categories.index') }}"
                               class="nav-link text-white sidebar-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                                <i class="bi bi-list me-2"></i> Listar categorías
                            </a>
                        </li>
                        @can('catalog.manage')
                        <li class="nav-item">
                            <a href="{{ route('admin.categories.create') }}"
                               class="nav-link text-white sidebar-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">
                                <i class="bi bi-plus-circle me-2"></i> Nueva categoría
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>

            {{-- Productos --}}
            <li class="nav-item">
                @php $productsActive = request()->routeIs('admin.products.*'); @endphp
                <a href="#menu-products"
                   class="nav-link text-white sidebar-link d-flex justify-content-between align-items-center {{ $productsActive ? 'active' : '' }}"
                   data-bs-toggle="collapse" aria-expanded="{{ $productsActive ? 'true' : 'false' }}">
                    <span><i class="bi bi-box-seam me-2"></i> Productos</span>
                    <i class="bi bi-chevron-down sidebar-chevron {{ $productsActive ? 'rotated' : '' }}"></i>
                </a>
                <div class="collapse {{ $productsActive ? 'show' : '' }}" id="menu-products">
                    <ul class="nav flex-column ms-3 mt-1 gap-1 border-start border-secondary ps-2">
                        <li class="nav-item">
                            <a href="{{ route('admin.products.index') }}"
                               class="nav-link text-white sidebar-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                                <i class="bi bi-list me-2"></i> Listar productos
                            </a>
                        </li>
                        @can('catalog.manage')
                        <li class="nav-item">
                            <a href="{{ route('admin.products.create') }}"
                               class="nav-link text-white sidebar-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                                <i class="bi bi-plus-circle me-2"></i> Nuevo producto
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        {{-- ===== PEDIDOS ===== --}}
        @can('orders.view')
        <li class="nav-item">
            <a href="{{ route('admin.orders.index') }}"
               class="nav-link text-white sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart-check me-2"></i> Pedidos
            </a>
        </li>
        @endcan

        {{-- ===== ENVÍOS ===== --}}
        @can('shipments.view')
        <li class="nav-item">
            <a href="{{ route('admin.shipments.index') }}"
               class="nav-link text-white sidebar-link {{ request()->routeIs('admin.shipments.*') ? 'active' : '' }}">
                <i class="bi bi-truck me-2"></i> Envíos
            </a>
        </li>
        @endcan

        {{-- ===== CLIENTES ===== --}}
        @can('customers.view')
        <li class="nav-item">
            @php $customersActive = request()->routeIs('admin.customers.*'); @endphp
            <a href="#menu-customers"
               class="nav-link text-white sidebar-link d-flex justify-content-between align-items-center {{ $customersActive ? 'active' : '' }}"
               data-bs-toggle="collapse" aria-expanded="{{ $customersActive ? 'true' : 'false' }}">
                <span><i class="bi bi-people me-2"></i> Clientes</span>
                <i class="bi bi-chevron-down sidebar-chevron {{ $customersActive ? 'rotated' : '' }}"></i>
            </a>
            <div class="collapse {{ $customersActive ? 'show' : '' }}" id="menu-customers">
                <ul class="nav flex-column ms-3 mt-1 gap-1 border-start border-secondary ps-2">
                    <li class="nav-item">
                        <a href="{{ route('admin.customers.index') }}"
                           class="nav-link text-white sidebar-link {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
                            <i class="bi bi-list me-2"></i> Listar clientes
                        </a>
                    </li>
                    @can('customers.manage')
                    <li class="nav-item">
                        <a href="{{ route('admin.customers.create') }}"
                           class="nav-link text-white sidebar-link {{ request()->routeIs('admin.customers.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle me-2"></i> Nuevo cliente
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
        @endcan

        {{-- ===== USUARIOS DEL LOCAL ===== --}}
        @can('users.manage')
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}"
               class="nav-link text-white sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge me-2"></i> Usuarios del local
            </a>
        </li>
        @endcan

    </ul>

    <hr class="border-secondary">

    {{-- Usuario logueado --}}
    <div class="user-panel d-flex justify-content-between align-items-center gap-2 p-2 rounded">
        <div class="d-flex align-items-center gap-2 text-white flex-grow-1 overflow-hidden">
            <i class="bi bi-person-circle fs-3 text-secondary"></i>
            <div class="lh-sm overflow-hidden">
                <span class="d-block fw-semibold text-truncate" style="font-size:0.9rem">{{ auth()->user()->name }}</span>
                <span class="text-secondary text-truncate d-block" style="font-size:0.75rem">
                    {{ ucfirst(auth()->user()->roles->first()?->name ?? 'Usuario') }}
                </span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light border-0 logout-btn" title="Cerrar sesión">
                <i class="bi bi-box-arrow-right fs-5"></i>
            </button>
        </form>
    </div>
</div>

<style>
.sidebar-link {
    border-radius: 6px;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.sidebar-link:hover:not(.active) {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffffff !important;
}

.sidebar-chevron {
    font-size: 0.75rem;
    transition: transform 0.25s ease;
}

.sidebar-chevron.rotated {
    transform: rotate(-180deg);
}

.user-panel {
    background-color: rgba(255, 255, 255, 0.05);
    transition: background-color 0.2s ease;
}

.user-panel:hover {
    background-color: rgba(255, 255, 255, 0.08);
}

.logout-btn {
    color: #dee2e6;
    transition: color 0.2s ease, background-color 0.2s ease;
}

.logout-btn:hover {
    color: #ff6b6b;
    background-color: rgba(255, 107, 107, 0.1);
}
</style>

<script>
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(trigger => {
        const target = document.querySelector(trigger.getAttribute('href'));
        const chevron = trigger.querySelector('.sidebar-chevron');

        target.addEventListener('show.bs.collapse', () => chevron.classList.add('rotated'));
        target.addEventListener('hide.bs.collapse', () => chevron.classList.remove('rotated'));
    });
</script>