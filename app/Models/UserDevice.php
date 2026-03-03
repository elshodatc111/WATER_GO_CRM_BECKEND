<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model{

    protected $table = 'user_devices';
    protected $fillable = [
        'user_id',
        'fcm_token',
        'device_type',
        'device_name',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeAndroid($query){
        return $query->where('device_type', 'android');
    }

    public function scopeIos($query){
        return $query->where('device_type', 'ios');
    }
}
