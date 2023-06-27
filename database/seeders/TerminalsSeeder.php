<?php

namespace Database\Seeders;

use App\Models\Terminal;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TerminalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Terminal::create([
            'name' => 'DNS Google 1',
            'ipv4' => '8.8.8.8',
            'description' => 'DNS primario de Google',
            'user_id' => 1,
        ]);

        Terminal::create([
            'name' => 'DNS Google 2',
            'ipv4' => '8.8.4.4',
            'description' => 'DNS secundario de Google',
            'user_id' => 1,
        ]);

        Terminal::create([
            'name' => 'Localhost',
            'ipv4' => '127.0.0.1',
            'description' => '',
            'user_id' => 1,
        ]);

        Terminal::create([
            'name' => 'Desconocido',
            'ipv4' => '112.1.43.45',
            'description' => 'Este termnal no responde',
            'enabled' => false,
            'user_id' => 1,
        ]);

        Terminal::create([
            'name' => 'Desconocido 2',
            'ipv4' => '190.17.45.10',
            'description' => 'Este termnal no responde',
            'enabled' => false,
            'user_id' => 1,
        ]);
    }
}
