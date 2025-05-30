<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // \DB::table('role_has_permissions')->truncate();
        // \DB::table('model_has_roles')->truncate();
        // \DB::table('model_has_permissions')->truncate();
        // \DB::table('roles')->truncate();
        // \DB::table('permissions')->truncate();

        $permissions = [
            'pos',
            'backend',
            'view departments', 'create departments', 'edit departments', 'delete departments',
            'view product types', 'create product types', 'edit product types', 'delete product types',
            'view brands', 'create brands', 'edit brands', 'delete brands',
            'view colors', 'create colors', 'edit colors', 'delete colors',
            'view size', 'create size', 'edit size', 'delete size',
            'view size scales', 'create size scales', 'edit size scales', 'delete size scales',
            'view products', 'create products', 'edit products', 'delete products',
            'view collections', 'create collections', 'edit collections', 'delete collections',
            'view coupons', 'create coupons', 'edit coupons', 'delete coupons',
            'view print barcodes', 'create print barcodes', 'edit print barcodes', 'delete print barcodes',
            'view tags', 'create tags', 'edit tags', 'delete tags',
            'view branches', 'create branches', 'edit branches', 'delete branches',
            'view inventory transfer', 'create inventory transfer', 'edit inventory transfer', 'delete inventory transfer',
            'view customers', 'create customers', 'edit customers', 'delete customers',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'view users', 'create users', 'edit users', 'delete users',
            'view roles & permissions', 'edit roles & permissions',
            'view price reasons', 'create price reasons', 'edit price reasons', 'delete price reasons',
            'view settings', 'create settings', 'edit settings', 'delete settings',
            'view tax', 'create tax', 'edit tax', 'delete tax',
            'view role', 'create role', 'edit role', 'delete role',
            'view webpages', 'create webpages', 'edit webpages', 'delete webpages'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'Super Admin' => Permission::all(),
            'Admin' => Permission::all(),
            'Manager' => ['backend', 'view customers'],
            'Sales Person' => ['pos'],
            'Customer' => []
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if (is_array($permissions)) {
                $role->givePermissionTo($permissions);
            } else {
                $role->givePermissionTo($permissions);
            }
        }
    }
}
