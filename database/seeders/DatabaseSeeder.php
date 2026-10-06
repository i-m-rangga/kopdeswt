<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default users (password: 'password')
        Pengguna::create([
            'nama' => 'Budi Santoso',
            'username' => 'budi.kasir',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'status' => 'aktif',
        ]);

        Pengguna::create([
            'nama' => 'Sari Wulandari',
            'username' => 'sari.gudang',
            'password' => bcrypt('password'),
            'role' => 'petugas_persediaan',
            'status' => 'aktif',
        ]);

        Pengguna::create([
            'nama' => 'Ahmad Fauzi',
            'username' => 'ahmad.pengurus',
            'password' => bcrypt('password'),
            'role' => 'pengurus',
            'status' => 'aktif',
        ]);
    }
}