<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            ['name' => 'Departments', 'slug' => 'departments'],
            ['name' => 'Product Types', 'slug' => 'product types'],
            ['name' => 'Brands', 'slug' => 'brands'],
            ['name' => 'Colors', 'slug' => 'colors'],
            ['name' => 'Size', 'slug' => 'size'],
            ['name' => 'Size Scales', 'slug' => 'size scales'],
            ['name' => 'Products', 'slug' => 'products'],
            ['name' => 'Collections', 'slug' => 'collections'],
            ['name' => 'Email Templates', 'slug' => 'email templates'],
            ['name' => 'Print Barcodes', 'slug' => 'print barcodes'],
            ['name' => 'Tags', 'slug' => 'tags'],
            ['name' => 'Branches', 'slug' => 'branches'],
            ['name' => 'Inventory Transfer', 'slug' => 'inventory transfer'],
            ['name' => 'Purchase Order', 'slug' => 'purchase order'],
            ['name' => 'Product Web Configuration', 'slug' => 'product web configuration'],
            ['name' => 'Customers', 'slug' => 'customers'],
            ['name' => 'Suppliers', 'slug' => 'suppliers'],
            ['name' => 'Users', 'slug' => 'users'],
            ['name' => 'Coupons', 'slug' => 'coupons'],
            ['name' => 'Roles & Permissions', 'slug' => 'roles & permissions'],
            ['name' => 'Price Reasons', 'slug' => 'price reasons'],
            ['name' => 'Settings', 'slug' => 'settings'],
            ['name' => 'Tax', 'slug' => 'tax'],
            ['name' => 'Role', 'slug' => 'role'],
            ['name' => 'Webpages', 'slug' => 'webpages']
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}