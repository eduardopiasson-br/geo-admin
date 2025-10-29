<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed usuários padrão para desenvolvimento.
     */
    public function run(): void
    {
        // Criar usuário admin padrão
        User::updateOrCreate(
            ['email' => 'admin@geo-admin.local'],
            [
                'name' => 'Administrador',
                'email' => 'admin@geo-admin.local',
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // Senha padrão: password
            ]
        );

        $this->command->info('Usuário admin criado:');
        $this->command->info('  Email: admin@geo-admin.local');
        $this->command->info('  Senha: password');

        // Criar usuário teste adicional (opcional)
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Usuário de Teste',
                'email' => 'test@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        $this->command->info('Usuário de teste criado:');
        $this->command->info('  Email: test@example.com');
        $this->command->info('  Senha: password');
    }
}

