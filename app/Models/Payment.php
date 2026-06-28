<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'method',
        'amount',
        'status',
        'reference',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'order_id' => 'integer',
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    const METHODS = [
        'mercadopago' => 'Mercado Pago',
    ];

    const STATUSES = [
        'pendiente' => 'Pendiente',
        'aprobado'  => 'Aprobado',
        'rechazado' => 'Rechazado',
    ];

    const STATUS_COLORS = [
        'pendiente' => 'warning',
        'aprobado'  => 'success',
        'rechazado' => 'danger',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }
}
