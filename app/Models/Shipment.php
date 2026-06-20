<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id', 'carrier', 'tracking_number', 'status', 'shipped_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
    ];

    const STATUSES = [
        'en_preparacion' => 'En preparación',
        'enviado'        => 'Enviado',
    ];

    const STATUS_COLORS = [
        'en_preparacion' => 'warning',
        'enviado'        => 'primary',
    ];

    public function order() { return $this->belongsTo(Order::class); }

    public function statusLabel(): string { return self::STATUSES[$this->status] ?? $this->status; }
    public function statusColor(): string { return self::STATUS_COLORS[$this->status] ?? 'secondary'; }
}