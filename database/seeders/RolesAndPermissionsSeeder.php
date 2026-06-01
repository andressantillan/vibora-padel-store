<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir permisos por módulo
        $permissions = [
            // Catálogo
            'catalog.view', 'catalog.manage',
            // Stock / Variantes
            'stock.view', 'stock.manage',
            // Pedidos
            'orders.view', 'orders.manage',
            // Pagos
            'payments.manage',
            // Envíos
            'shipments.view', 'shipments.manage',
            // Clientes
            'customers.view', 'customers.manage',
            // Usuarios del local
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ----- ADMIN: todos los permisos -----
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // ----- VENDEDOR -----
        $vendedor = Role::firstOrCreate(['name' => 'vendedor']);
        $vendedor->syncPermissions([
            'catalog.view',
            'stock.view',
            'orders.view', 'orders.manage',
            'payments.manage',
            'shipments.view',
            'customers.view', 'customers.manage',
        ]);

        // ----- DEPÓSITO -----
        $deposito = Role::firstOrCreate(['name' => 'deposito']);
        $deposito->syncPermissions([
            'catalog.view', 'catalog.manage',
            'stock.view', 'stock.manage',
            'orders.view',
            'shipments.view', 'shipments.manage',
            'customers.view',
        ]);
    }
}