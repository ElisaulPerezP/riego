<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear el usuario administrador
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@arandanosdemipueblo.online',
            'password' => Hash::make('password'), // Cambia "password" por una contraseña segura
        ]);

        // Crear el rol de administrador si no existe
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Asignar el rol al usuario administrador
        $admin->assignRole($role);
    }
}
