<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model{
    use SoftDeletes;
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'price',
        'image',
        'image_banner',
        'is_active',
        'created_by',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function creator(){
        return $this->belongsTo(User::class, 'created_by');
    }
    public function scopeActive($query){
        return $query->where('is_active', true);
    }
    public function getImageUrlAttribute(){
        return $this->image ? asset($this->image) : null;
    }
    public function getBannerUrlAttribute(){
        return $this->image_banner ? asset($this->image_banner) : null;
    }
}
