<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Psy\Sudo;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Position;
use App\Models\Department;
use App\Models\TaskType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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
        $this->createUser([
            'name' => 'Employee',
            'email' => 'employee123@gmail.com',
            'role' => 'employee',
            'password' => 'Employee123@'
        ]);
        Customer::factory(10)->create();
        Company::factory(10)->create();
        Department::factory(10)->create();
        Position::factory(10)->create();
        User::factory(10)->create();
        Project::factory(10)->create();
        TaskType::factory(10)->create();
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
