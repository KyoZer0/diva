<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full access to all features and data'
            ],
            [
                'name' => 'rep',
                'display_name' => 'Sales Representative',
                'description' => 'Can manage their own clients and view analytics'
            ],
            [
                'name' => 'facturation',
                'display_name' => 'Facturation',
                'description' => 'Can manage invoices and view client data'
            ]
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@divaceramica.com',
            'password' => Hash::make('password123'),
        ]);

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole);

        // Create sample rep user
        $rep = User::create([
            'name' => 'Sales Rep',
            'email' => 'rep@divaceramica.com',
            'password' => Hash::make('password123'),
        ]);

        // Assign rep role
        $repRole = Role::where('name', 'rep')->first();
        $rep->roles()->attach($repRole);

        // Create sample facturation user
        $facturation = User::create([
            'name' => 'Facturation User',
            'email' => 'facturation@divaceramica.com',
            'password' => Hash::make('password123'),
        ]);

        // Assign facturation role
        $facturationRole = Role::where('name', 'facturation')->first();
        $facturation->roles()->attach($facturationRole);
    }
}
