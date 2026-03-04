<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'description' => 'Handles recruitment and employee relations',
            ],
            [
                'name' => 'IT',
                'description' => 'Manages software development and IT infrastructure',
            ],
            [
                'name' => 'Sales',
                'description' => 'Handles sales and client relationships',
            ],
            [
                'name' => 'Finance',
                'description' => 'Manages company financial operations',
            ],
            [
                'name' => 'Marketing',
                'description' => 'Handles branding, promotions and advertising',
            ],
            [
                'name' => 'Operations',
                'description' => 'Oversees daily business operations',
            ],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->updateOrInsert(
                ['name' => $department['name']],
                [
                    'description' => $department['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}