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
            ]
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Get role instances
        $adminRole = Role::where('name', 'admin')->first();
        $repRole = Role::where('name', 'rep')->first();

        // Create Sales Representatives
        $reps = [
            ['name' => 'Sekkat', 'email' => 'sekkat@divaceramica.com'],
            ['name' => 'Khalid', 'email' => 'khalid@divaceramica.com'],
            ['name' => 'Yousef', 'email' => 'yousef@divaceramica.com'],
            ['name' => 'Yassir', 'email' => 'yassir@divaceramica.com'],
            ['name' => 'Hatim', 'email' => 'hatim@divaceramica.com'],
            ['name' => 'Oumaima', 'email' => 'oumaima@divaceramica.com'],
        ];

        foreach ($reps as $repData) {
            $user = User::create([
                'name' => $repData['name'],
                'email' => $repData['email'],
                'password' => Hash::make('password123'),
            ]);
            $user->roles()->attach($repRole);
        }

        // Create Admins
        $admins = [
            ['name' => 'Mery', 'email' => 'mery@divaceramica.com'],
            ['name' => 'Mlhlou', 'email' => 'mlhlou@divaceramica.com'],
            ['name' => 'IT', 'email' => 'it@divaceramica.com'],
        ];

        foreach ($admins as $adminData) {
            $user = User::create([
                'name' => $adminData['name'],
                'email' => $adminData['email'],
                'password' => Hash::make('password123'),
            ]);
            $user->roles()->attach($adminRole);
        }
    }
}