<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createUser([
            'name' => 'Owner',
            'email' => 'owner123@gmail.com',
            'role' => 'owner',
            'password' => 'Owner123@'
        ]);
        $this->createUser([
            'name' => 'Admin',
            'email' => 'admin123@gmail.com',
            'role' => 'admin',
            'password' => 'Admin123@'
        ]);
    }
    private function createUser(array $data): void
    {
        User::create([
            'name' => $data['name'],
            'company_id' => null,
            'position_id' => null,
            'password' => Hash::make($data['password']),
            'email' => $data['email'],
            'role' => $data['role'],
            'phone' => null,
            'gender' => null,
            'nrc_number' => null,
            'department_id' => null,
            'photo_path' => null
        ]);
    }
}
