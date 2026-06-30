<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Stock;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI: Ventas del mes
        $currentMonthSales = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('payment_status', 'pagado')
            ->sum('total');

        $lastMonthSales = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->where('payment_status', 'pagado')
            ->sum('total');

        // KPI: Pedidos por despachar
        $pendingShipments = Order::whereIn('fulfillment_status', ['sin_preparar', 'preparado'])
            ->count();

        // KPI: Alertas de Stock
        $lowStockCount = Stock::whereColumn('quantity', '<=', 'min_quantity')->count();

        // Gráfico A: Ventas últimos 30 días
        $salesLast30Days = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('payment_status', 'pagado')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();
        
        $salesDates = $salesLast30Days->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d/m'))->toArray();
        $salesTotals = $salesLast30Days->pluck('total')->toArray();

        // Gráfico B: Estado de los pedidos
        $orderStatuses = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')->toArray();

        // Rellenar estados básicos para el donut
        $donutLabels = ['pendiente', 'pagado', 'enviado', 'cancelado'];
        $donutSeries = [];
        foreach ($donutLabels as $label) {
            $donutSeries[] = $orderStatuses[$label] ?? 0;
        }
        $donutLabels = array_map('ucfirst', $donutLabels);

        // Gráfico C: Top 5 productos
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.payment_status', 'pagado')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderBy('total_sold', 'DESC')
            ->limit(5)
            ->get();
        
        $topProductNames = $topProducts->pluck('name')->toArray();
        $topProductTotals = $topProducts->pluck('total_sold')->toArray();

        return view('dashboard', compact(
            'currentMonthSales', 'lastMonthSales', 'pendingShipments', 'lowStockCount',
            'salesDates', 'salesTotals', 'donutLabels', 'donutSeries', 'topProductNames', 'topProductTotals'
        ));
    }
}
