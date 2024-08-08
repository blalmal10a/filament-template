<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $su = User::create([
            'name' => 'superuser',
            'email' => 'su@example.email',
            'password' => bcrypt('su@example.email')
        ]);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.email',
            'password' => bcrypt('admin@example.email')
        ]);
        $this->call([
            ShieldSeeder::class,
        ]);
        $su->assignRole('super_admin');
        $admin->assignRole('admin');
    }
}
