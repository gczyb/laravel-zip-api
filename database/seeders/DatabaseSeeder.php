<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Töröljük a meglévő felhasználókat, hogy ne legyen duplikáció
        User::truncate();

        // 2. Létrehozzuk az Admint
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'), // Biztosan jó titkosítás
        ]);
        
        $this->command->info('Admin felhasználó létrehozva!');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Jelszó: password');
    }
}