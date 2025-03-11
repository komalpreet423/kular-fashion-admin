<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\ProductType;
use App\Models\ProductTypeDepartment;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Men'],
            ['name' => 'Women'],
            ['name' => 'Unisex']
        ];

        // Store created departments in an array for easy reference
        $createdDepartments = [];
        foreach ($departments as $departmentData) {
            $createdDepartments[$departmentData['name']] = Department::create($departmentData);
        }

        $productTypes = [
            ['name' => 'Accessories', 'short_name' => 'ACC', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Bag', 'short_name' => 'BAG', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Beanies Cap', 'short_name' => 'BCA', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Belt', 'short_name' => 'BLT', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Boxer Shorts', 'short_name' => 'BXR', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Casual Jacket', 'short_name' => 'CJK', 'departments' => ['Women']],
            ['name' => 'Cords', 'short_name' => 'CRD', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Denim Jacket', 'short_name' => 'DEN', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Dress', 'short_name' => 'DRE', 'departments' => ['Women']],
            ['name' => 'Fleeces', 'short_name' => 'FLE', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Fragrances', 'short_name' => 'PER', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Glasses', 'short_name' => 'GLA', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Gloves', 'short_name' => 'GLO', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Hoodies', 'short_name' => 'HOD', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Jeans', 'short_name' => 'JEA', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Joggin Bottoms', 'short_name' => 'JOG', 'departments' => ['Women']],
            ['name' => 'Jumper', 'short_name' => 'JUM', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'L/S Polo Shirts', 'short_name' => 'PSH', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Leather Jacket', 'short_name' => 'LJK', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Packaging', 'short_name' => 'PAC', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Pants', 'short_name' => 'PAN', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Peak Caps', 'short_name' => 'CAP', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Purse', 'short_name' => 'PUR', 'departments' => ['Women']],
            ['name' => 'S/S Polo Shirts', 'short_name' => 'SSP', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Scarf', 'short_name' => 'SCA', 'departments' => ['Unisex']],
            ['name' => 'Shirts', 'short_name' => 'SHI', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Shoes', 'short_name' => 'BOT', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Shorts', 'short_name' => 'SHR', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Skirt', 'short_name' => 'SKT', 'departments' => ['Unisex']],
            ['name' => 'Socks', 'short_name' => 'SOC', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Suits', 'short_name' => 'SUT', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Sweatshirts', 'short_name' => 'SWT', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'T-Shirts', 'short_name' => 'TSH', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Ties', 'short_name' => 'TIE', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Tracksuits', 'short_name' => 'TRK', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Trainers', 'short_name' => 'TRA', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Trousers', 'short_name' => 'TRO', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Waistcoats', 'short_name' => 'WST', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => 'Wallets', 'short_name' => 'WAL', 'departments' => ['Men']],
            ['name' => 'Watches', 'short_name' => 'WAT', 'departments' => ['Men', 'Women', 'Unisex']],
            ['name' => '1/4 Jumper', 'short_name' => '1/4 JU', 'departments' => ['Unisex']],
            ['name' => '1/4 Sweatshirt', 'short_name' => '1/4 SS', 'departments' => ['Men', 'Women', 'Unisex']],
        ];


        foreach ($productTypes as $productTypeData) {
            $productType = ProductType::create([
                'name'       => $productTypeData['name'],
                'short_name' => $productTypeData['short_name']
            ]);

            foreach ($productTypeData['departments'] as $departmentName) {
                $department = $createdDepartments[$departmentName];

                ProductTypeDepartment::create([
                    'product_type_id' => $productType->id,
                    'department_id'   => $department->id
                ]);
            }
        }
    }
}
