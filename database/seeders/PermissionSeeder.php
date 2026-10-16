<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $rolesPermissions = [
            'admin' => [
                'barberos.ver', 'barberos.crear', 'barberos.editar', 'barberos.eliminar',
                'barberos-servicios.ver', 'barberos-servicios.crear', 'barberos-servicios.editar', 'barberos-servicios.eliminar',
                'categorias-servicios.ver', 'categorias-servicios.crear', 'categorias-servicios.editar', 'categorias-servicios.eliminar',
                'categorias-productos.ver', 'categorias-productos.crear', 'categorias-productos.editar', 'categorias-productos.eliminar',
                'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
                'movimientos-stock.ver', 'movimientos-stock.crear', 'movimientos-stock.editar', 'movimientos-stock.eliminar',
                'horarios.ver', 'horarios.crear', 'horarios.editar', 'horarios.eliminar',
                'descansos.ver', 'descansos.crear', 'descansos.editar', 'descansos.eliminar',
                'servicios.ver', 'servicios.crear', 'servicios.editar', 'servicios.eliminar',
                'citas.ver', 'citas.crear', 'citas.administrar', 'citas.historial',
                'ordenes-estados.ver', 'ordenes-estados.crear', 'ordenes-estados.editar', 'ordenes-estados.eliminar',
                'ordenes.ver', 'ordenes.administrar',
                'ventas.ver', 'ventas.crear', 'ventas.editar', 'ventas.eliminar', 'ventas.reportes',
                'cuentas-por-cobrar.ver', 'cuentas-por-cobrar.administrar', 'cuentas-por-cobrar.reportes', 'cuentas-por-cobrar-pagos.ver', 'cuentas-por-cobrar-pagos.administrar', 'cuentas-por-cobrar-pagos.reportes',
                'compras.ver', 'compras.crear', 'compras.editar', 'compras.eliminar', 'compras.reportes',
                'cuentas-por-pagar.ver', 'cuentas-por-pagar.administrar', 'cuentas-por-pagar.reportes', 'cuentas-por-pagar-pagos.ver', 'cuentas-por-pagar-pagos.administrar', 'cuentas-por-pagar-pagos.reportes',
                'cajas.ver', 'cajas.crear', 'cajas.editar', 'cajas.eliminar', 'cajas.cerrar', 'cajas.movimientos',
                'formas-pago.ver', 'formas-pago.crear', 'formas-pago.editar', 'formas-pago.eliminar',
                'gastos.ver', 'gastos.crear', 'gastos.editar', 'gastos.eliminar', 'gastos.reportes',
                'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
                'proveedores.ver', 'proveedores.crear', 'proveedores.editar', 'proveedores.eliminar',
                'galeria-comercial.ver', 'galeria-comercial.crear', 'galeria-comercial.editar', 'galeria-comercial.eliminar',
                'roles.ver', 'roles.crear', 'roles.editar', 'roles.eliminar',
                'administracion.ver', 'administracion.editar', 'administracion.calificaciones', 'administracion.tablero',
            ],
        ];

        foreach ($rolesPermissions as $roleName => $permissions) {
            // Crear rol
            $role = Role::firstOrCreate(['name' => $roleName]);

            foreach ($permissions as $permName) {
                // Crear permiso si no existe
                $permission = Permission::firstOrCreate(['name' => $permName]);
                // Asignar permiso al rol
                $role->givePermissionTo($permission);
            }
        }
    }
}
