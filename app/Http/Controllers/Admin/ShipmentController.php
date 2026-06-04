<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Shipment;
use App\Services\OrderStatusManager;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShipmentController extends Controller
{
    public function __construct(protected OrderStatusManager $statusManager) {}

    public function index(Request $request)
    {
        $query = Shipment::with('order.customer.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tracking_number', 'ilike', "%{$search}%")
                ->orWhereHas('order.customer.user', function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                      ->orWhere('email', 'ilike', "%{$search}%");
                });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('carrier')) {
            $query->where('carrier', 'ilike', "%{$request->carrier}%");
        }

        $shipments = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.shipments.index', compact('shipments'));
    }

    public function store(StoreShipmentRequest $request)
    {
        $order = Order::findOrFail($request->order_id);

        if ($order->payment_status !== 'pagado') {
            return redirect()->route('admin.orders.show', $order->id)
                ->with('error', 'No se puede registrar un envío sin el pago registrado.');
        }

        DB::transaction(function () use ($request) {
            $shipment = Shipment::create($request->validated());
            $this->statusManager->onShipmentSent($shipment);
        });

        return redirect()->route('admin.orders.show', $request->order_id)
            ->with('success', 'Envío registrado correctamente.');
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        DB::transaction(function () use ($request, $shipment) {
            $shipment->update($request->validated());
            $this->statusManager->onShipmentSent($shipment->fresh());
        });

        return redirect()->route('admin.orders.show', $shipment->order_id)
            ->with('success', 'Envío actualizado correctamente.');
    }

    public function destroy(Shipment $shipment)
    {
        $orderId = $shipment->order_id;
        $shipment->delete();

        return redirect()->route('admin.orders.show', $orderId)
            ->with('success', 'Envío eliminado correctamente.');
    }
}