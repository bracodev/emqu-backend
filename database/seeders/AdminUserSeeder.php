<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Brayan Rincón',
            'email' => 'brayan262@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        User::create([
            'name' => 'EmQu',
            'email' => 'contacto@emqu.net',
            'password' => bcrypt('123456'),
        ]);
    }
}
