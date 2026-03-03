<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable{

    use HasApiTokens, Notifiable, SoftDeletes;
    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'password',
        'role',
        'is_active',
        'sms_code_hash',
        'sms_code_expires_at',
        'phone_verified_at',
    ];
    
    protected $hidden = ['password','remember_token','sms_code_hash',];
    protected $casts = ['is_active' => 'boolean','phone_verified_at' => 'datetime','sms_code_expires_at' => 'datetime','deleted_at' => 'datetime',];
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function isAdmin(): bool{
        return $this->role === 'admin';
    }
    public function isDirector(): bool{
        return $this->role === 'director';
    }
    public function isCourier(): bool{
        return $this->role === 'courier';
    }
    public function isClient(): bool{
        return $this->role === 'client';
    }
    public function isActive(): bool{
        return $this->is_active;
    }
    public function hasVerifiedPhone(): bool{
        return !is_null($this->phone_verified_at);
    }
    public function smsCodeExpired(): bool{
        return $this->sms_code_expires_at && now()->greaterThan($this->sms_code_expires_at);
    }
    public function scopeActive($query){
        return $query->where('is_active', true);
    }
    public function scopeClients($query){
        return $query->where('role', 'client');
    }
    public function scopeStaff($query){
        return $query->whereIn('role', ['director', 'courier']);
    }
    public function scopeForCompany($query, $companyId){
        return $query->where('company_id', $companyId);
    }
    public function devices(){
        return $this->hasMany(UserDevice::class);
    }
    
}