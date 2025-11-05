<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PartSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('parts')->insert([
            'name' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('parts')->insert([
            'name' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('parts')->insert([
            'name' => '3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('parts')->insert([
            'name' => '4',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('parts')->insert([
            'name' => '5',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('parts')->insert([
            'name' => '6',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('parts')->insert([
            'name' => '7',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
