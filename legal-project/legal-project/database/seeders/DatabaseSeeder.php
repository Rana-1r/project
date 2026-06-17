<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // 2. زراعة الأدوار الخمسة الرسمية بمسمياتها الصحيحة والمعرّفات المرتبة
        DB::table('roles')->insert([
            ['role_id' => 1, 'role_name' => 'manager', 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'role_name' => 'employee', 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'role_name' => 'internal_employee', 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 4, 'role_name' => 'it', 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 5, 'role_name' => 'top_management', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}