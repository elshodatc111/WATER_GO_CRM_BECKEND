<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBalanceTransaction extends Model{

    protected $table = 'company_balance_transactions';
    
    protected $fillable = [
        'company_id',
        'created_by',
        'type',
        'amount',
        'balance_joriy',
        'balance_kiyingi',
        'description',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_joriy' => 'decimal:2',
        'balance_kiyingi' => 'decimal:2',
        'created_at' => 'datetime',
    ];
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function creator(){
        return $this->belongsTo(User::class, 'created_by');
    }
    public function scopeNaqt($query){
        return $query->where('type', 'naqt');
    }
    public function scopeCard($query){
        return $query->where('type', 'card');
    }
    public function scopeReturn($query){
        return $query->where('type', 'return');
    }
    public function getFormattedAmountAttribute(){
        return number_format($this->amount, 0, ',', ' ') . ' UZS';
    }
    public function getFormattedBalanceJoriyAttribute(){
        return number_format($this->balance_joriy, 0, ',', ' ') . ' UZS';
    }
    public function getFormattedBalanceKiyingiAttribute(){
        return number_format($this->balance_kiyingi, 0, ',', ' ') . ' UZS';
    }
}
