<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Admin User',
            'password' => bcrypt('adminpassword'), // password
        ]);
        Admin::factory(5)->create();
    }
}
