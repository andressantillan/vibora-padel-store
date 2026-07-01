@extends('layouts.app')

@section('title', 'Dashboard - Víbora Padel')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Dashboard</h1>
    </div>

    <!-- KPIs Row -->
    <div class="row g-4 mb-4">
        <!-- Ventas del Mes -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Ventas (Este Mes)</div>
                            <div class="h5 mb-0 fw-bold">${{ number_format($currentMonthSales, 2) }}</div>
                            <small class="text-muted">Mes pasado: ${{ number_format($lastMonthSales, 2) }}</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fs-1 text-secondary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos por Despachar -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Por Despachar</div>
                            <div class="h5 mb-0 fw-bold">{{ $pendingShipments }} Pedidos</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fs-1 text-secondary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas de Stock -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                Alertas de Stock</div>
                            <div class="h5 mb-0 fw-bold">{{ $lowStockCount }} Variantes</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fs-1 text-secondary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-4 mb-4">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between border-0">
                    <h6 class="m-0 fw-bold text-primary">Evolución de Ventas (Últimos 30 días)</h6>
                </div>
                <div class="card-body">
                    <div id="salesChart"></div>
                </div>
            </div>
        </div>

        <!-- Donut Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between border-0">
                    <h6 class="m-0 fw-bold text-primary">Estado de Pedidos</h6>
                </div>
                <div class="card-body">
                    <div id="statusChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-4">
        <div class="col-xl-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3 border-0">
                    <h6 class="m-0 fw-bold text-primary">Top 5 Productos Más Vendidos</h6>
                </div>
                <div class="card-body">
                    <div id="topProductsChart"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. Gráfico de Ventas (Area) ---
        var salesOptions = {
            series: [{
                name: "Ventas ($)",
                data: {!! json_encode($salesTotals) !!}
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            theme: { mode: 'dark' },
            colors: ['#0d6efd'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: {!! json_encode($salesDates) !!},
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return "$" + val.toLocaleString();
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "$" + val.toLocaleString();
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#salesChart"), salesOptions).render();

        // --- 2. Gráfico de Estados (Donut) ---
        var statusOptions = {
            series: {!! json_encode($donutSeries) !!},
            chart: {
                type: 'donut',
                height: 350,
            },
            theme: { mode: 'dark' },
            labels: {!! json_encode($donutLabels) !!},
            colors: ['#ffc107', '#198754', '#0dcaf0', '#dc3545'],
            dataLabels: { enabled: true },
            legend: { position: 'bottom' }
        };
        new ApexCharts(document.querySelector("#statusChart"), statusOptions).render();

        // --- 3. Top Productos (Barra Horizontal) ---
        var topProductsOptions = {
            series: [{
                name: "Unidades Vendidas",
                data: {!! json_encode($topProductTotals) !!}
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            theme: { mode: 'dark' },
            colors: ['#6f42c1'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                }
            },
            dataLabels: { enabled: true },
            xaxis: {
                categories: {!! json_encode($topProductNames) !!},
            }
        };
        new ApexCharts(document.querySelector("#topProductsChart"), topProductsOptions).render();
    });
</script>
@endsection
