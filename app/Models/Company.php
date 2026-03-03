<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use League\Uri\Builder;

class Company extends Model{
    protected $fillable = [
        'company_name',
        'direktor',
        'phone',
        'address',
        'balance',
        'service_fee',
        'rating',
        'rating_count',
        'inn',
        'is_active',
        'description',
        'logo',
        'banner',
        'working_hours',
        'latitude',
        'longitude',
        'delivery_radius',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'balance' => 'decimal:7',
        'service_fee' => 'decimal:7',
    ];
    
    public function scopeActive(Builder $query): Builder{
        return $query->where('is_active', true);
    }
    
    public function scopeWithCoordinates(Builder $query): Builder{
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function scopeWithinRadius(Builder $query,float $userLat,float $userLng): Builder {
        return $query->select('*')->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude))
                )) AS distance', [$userLat, $userLng, $userLat])
            ->where('is_active', true)->whereNotNull('delivery_radius')->havingRaw('distance <= delivery_radius')
            ->orderBy('distance');
    }

    public function hasRating(): bool{
        return $this->rating_count > 0;
    }
    public function hasUnlimitedDelivery(): bool{
        return is_null($this->delivery_radius);
    }

}
