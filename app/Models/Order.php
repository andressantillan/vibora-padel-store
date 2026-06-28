<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'customer_id', 
        'address_id',
        'status', 
        'payment_status', 
        'fulfillment_status',
        'subtotal', 
        'discount', 
        'total', 
        'coupon_code',
    ];

    const PAYMENT_STATUSES = [
        'pendiente' => 'Pendiente',
        'pagado'    => 'Pagado',
    ];

    const FULFILLMENT_STATUSES = [
        'sin_preparar'   => 'Sin preparar',
        'en_preparacion' => 'En preparación',
        'enviado'        => 'Enviado',
    ];

    const STATUS_COLORS = [
        'pendiente'      => 'warning',
        'en_preparacion' => 'info',
        'enviado'        => 'primary',
        'cancelado'      => 'danger',
    ];

    const STATUS_LABELS = [
        'pendiente'      => 'Pendiente',
        'en_preparacion' => 'En preparación',
        'enviado'        => 'Enviado',
        'cancelado'      => 'Cancelado',
    ];

    public function deriveStatus(): string
    {
        if ($this->status === 'cancelado') {
            return 'cancelado';
        }

        if ($this->payment_status !== 'pagado') {
            return 'pendiente';
        }

        return $this->fulfillment_status === 'enviado' ? 'enviado' : 'en_preparacion';
    }

    public function paymentStatusLabel(): string { return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status; }
    public function fulfillmentStatusLabel(): string { return self::FULFILLMENT_STATUSES[$this->fulfillment_status] ?? $this->fulfillment_status; }
    public function statusLabel(): string { return self::STATUS_LABELS[$this->status] ?? $this->status; }
    public function statusColor(): string { return self::STATUS_COLORS[$this->status] ?? 'secondary'; }

    public function canBeCancelled(): bool { return $this->status === 'pendiente'; }
    public function isPaid(): bool { return $this->payment_status === 'pagado'; }

    public function customer()      { return $this->belongsTo(Customer::class); }
    public function address()       { return $this->belongsTo(Address::class); }
    public function items()         { return $this->hasMany(OrderItem::class); }
    public function statusHistory() { return $this->hasMany(OrderStatusHistory::class)->latest(); }
    public function shipment()      { return $this->hasOne(Shipment::class); }
    public function payments()      { return $this->hasMany(Payment::class); }
}


