<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Configuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default configurations
        Configuration::setValue('cuota_global', '10485760'); // 10MB
        Configuration::setValue('extensiones_prohibidas', 'exe,bat,js,php,sh');

        // Create admin user
        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create regular user
        $regularUser = User::create([
            'name' => 'Usuario Regular',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'usuario',
            'email_verified_at' => now(),
        ]);

        // Create test groups
        $marketingGroup = Group::create([
            'name' => 'Marketing',
            'description' => 'Equipo de marketing y publicidad',
            'quota' => 20971520, // 20MB
        ]);

        $desarrolloGroup = Group::create([
            'name' => 'Desarrollo',
            'description' => 'Equipo de desarrollo de software',
            'quota' => 52428800, // 50MB
        ]);

        // Assign users to groups
        $regularUser->update(['group_id' => $marketingGroup->id]);

        // Create additional test users
        User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => Hash::make('password123'),
            'role' => 'usuario',
            'group_id' => $desarrolloGroup->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => Hash::make('password123'),
            'role' => 'usuario',
            'group_id' => $marketingGroup->id,
            'email_verified_at' => now(),
        ]);

        // Create another admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
}
