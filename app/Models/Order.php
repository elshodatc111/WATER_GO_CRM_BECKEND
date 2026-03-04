<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_id',
        'total_count',
        'total_price',
        'delivery_price',
        'final_price',
        'status',
        'payment_method',
        'payment_status',
        'address',
        'latitude',
        'longitude',
        'courier_id',
        'courier_comment',
        'courier_rating',
        'courier_rating_text',
        'delivered_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'delivery_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'delivered_at' => 'datetime',
        'total_count' => 'integer',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo{
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function courier(): BelongsTo{
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function items(): HasMany{
        return $this->hasMany(OrderItem::class);
    }
}
