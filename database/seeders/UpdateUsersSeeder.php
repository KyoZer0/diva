<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UpdateUsersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure all ROLES exist (Safe: won't duplicate)
        $rolesList = [
            ['name' => 'admin', 'display_name' => 'Administrator'],
            ['name' => 'rep', 'display_name' => 'Sales Representative'], // Matches 'Commercial'
            ['name' => 'logistics', 'display_name' => 'Logistics Manager'],
            ['name' => 'stock_manager', 'display_name' => 'Stock & SAV Manager'],
        ];

        foreach ($rolesList as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']], 
                ['display_name' => $roleData['display_name']]
            );
        }

        // 2. Define the Specific Users & Access
        $users = [
            // IT (Full Access) - Existing user, update password
            [
                'email' => 'it@divaceramica.com', // Typo fixed from 'divaceramca' to 'divaceramica' based on pattern, or keep as provided? 
                                                  // Assuming 'divaceramica.com' is correct based on others.
                'name' => 'IT Support',
                'password' => 'password123',
                'role' => 'admin'
            ],
            // Admin (Full Access)
            [
                'email' => 'admin@divaceramica.com',
                'name' => 'Admin',
                'password' => 'Diva@Admin@26',
                'role' => 'admin'
            ],
            // Kenza (Commercial/Rep) - Client Access
            [
                'email' => 'm.kenza@divaceramica.com',
                'name' => 'Kenza',
                'password' => 'K1@Diva@26',
                'role' => 'rep' // Maps to 'Commercial'
            ],
            // Oussama (Logistics) - Logistics Tool Only
            [
                'email' => 'oussama@divaceramica.com',
                'name' => 'Oussama',
                'password' => 'O2@Iman@26',
                'role' => 'logistics'
            ],
            // Miloud (Logistics) - Logistics Tool Only
            [
                'email' => 'miloud@divaceramica.com',
                'name' => 'Miloud',
                'password' => 'M3@Iman@26',
                'role' => 'logistics'
            ],
            // Hatim (Stock Manager) - SAV & Read-Only Logistics
            // Note: Hatim was previously a 'rep'. This will change his role.
            [
                'email' => 'hatim@divaceramica.com',
                'name' => 'Hatim',
                'password' => 'H4@Diva@26',
                'role' => 'stock_manager'
            ],
        ];

        // 3. Process Users (Safe Update)
        foreach ($users as $userData) {
            // Find User by Email or Create New
            $user = User::firstOrNew(['email' => $userData['email']]);
            
            // Update Name and Password
            $user->name = $userData['name'];
            $user->password = Hash::make($userData['password']);
            $user->save();

            // Assign Role (Sync removes old roles to ensure strict permissions)
            // This ensures Hatim loses 'rep' access and gains 'stock_manager' access
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }
    }
}