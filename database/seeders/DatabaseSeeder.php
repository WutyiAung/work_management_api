<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Psy\Sudo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'admin',
            'company_id' => null,
            'position_id' => null,
            "password" => Hash::make('admin123'),
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'phone' => null,
             'gender' => null,
             'nrc_number' => null,
             'department_id' => null,
             'photo_path' => null
        ]);
        // $this->call([
        //     CompanyTableData::class
        // ]);
    }
}
