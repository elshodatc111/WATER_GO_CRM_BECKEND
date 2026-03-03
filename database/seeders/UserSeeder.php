<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder{
    public function run(): void{
        User::create([
            'name' => 'Super Admin',
            'phone' => '+998901234567',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'company_id' => null,
            'phone_verified_at' => now(),
        ]);

    }
}
