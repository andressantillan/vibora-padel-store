<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LocalUsersSeeder extends Seeder
{
    public function run(): void
    {
        // ----- VENDEDOR -----
        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor@vibora.com'],
            [
                'name'     => 'Vendedor de Prueba',
                'password' => Hash::make('vendedor123@'),
            ]
        );
        $vendedor->syncRoles(['vendedor']);

        // ----- DEPÓSITO -----
        $deposito = User::firstOrCreate(
            ['email' => 'deposito@vibora.com'],
            [
                'name'     => 'Depósito de Prueba',
                'password' => Hash::make('deposito123@'),
            ]
        );
        $deposito->syncRoles(['deposito']);

        $this->command->info('Usuarios del local creados:');
        $this->command->info('  vendedor@vibora.com / vendedor123@ (rol: vendedor)');
        $this->command->info('  deposito@vibora.com / deposito123@ (rol: deposito)');
    }
}