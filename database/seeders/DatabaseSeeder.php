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
            'password' => 'Owner123@',
            'company_id' => null
        ]);
        $this->createUser([
            'name' => 'Admin',
            'email' => 'admin123@gmail.com',
            'role' => 'admin',
            'password' => 'Admin123@',
            'company_id' => null
        ]);

        Customer::factory(10)->create();
        $this->createCompany([
            'name' => 'k-win'
        ]);
        $this->createCompany([
            'name' => 'beyond'
        ]);
        Company::factory(10)->create();
        $this->createUser([
            'name' => 'Min Thu Kha',
            'email' => 'minthukha@gmail.com',
            'role' => 'employee',
            'password' => '00000000',
            'company_id' => '1'
        ]);
        Department::factory(10)->create();
        Position::factory(10)->create();
        User::factory(10)->create();
        Project::factory(10)->create();
        $this->createTaskType([
            'name' => 'Graphic Design',
            'company_id' => '2',
            'table_name' => 'graphic_designs',
        ]);
        $this->createTaskType([
            'name' => 'Shooting',
            'company_id' => '2',
            'table_name' => 'shootings',
        ]);
        $this->createTaskType([
            'name' => 'Frontend',
            'company_id' => '1',
            'table_name' => 'front_ends',
        ]);
        $this->createTaskType([
            'name' => 'Backend',
            'company_id' => '1',
            'table_name' => 'back_ends',
        ]);
        $this->createTaskType([
            'name' => 'UiUx',
            'company_id' => '1',
            'table_name' => 'ui_uxes',
        ]);
        $this->createTaskType([
            'name' => 'Testing',
            'company_id' => '1',
            'table_name' => 'testings'
        ]);
        $this->createTaskType([
            'name' => 'Deployment',
            'company_id' => '1',
            'table_name' => 'deployments',
        ]);
        TaskType::factory(10)->create();
    }
    private function createUser(array $data): void
    {
        User::create([
            'name' => $data['name'],
            'company_id' => $data['company_id'],
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
    private function createTaskType(array $data): void
    {
        TaskType::create([
            'name' => $data['name'],
            'company_id' => $data['company_id'],
            'table_name' => $data['table_name'],
            'table_type' => 'Fixed'
        ]);
    }
    private function createCompany(array $data): void
    {
        Company::create([
            'name' => $data['name']
        ]);
    }
}
