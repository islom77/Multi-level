<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        if(count($admin) > 0) {
            return;
        }

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'), // parolni xohlagancha o‘zgartirishingiz mumkin
            // 'role' => 'admin', // agar sizda role ustuni bo‘lsa
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
