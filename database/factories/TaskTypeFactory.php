<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskType>
 */
class TaskTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = implode(' ', fake()->words(2));
        $name = ucwords($name);
        $tableName = implode('_', fake()->words(3));
        $tableName = strtolower($tableName);
        return [
            'name' => $name,
            'company_id' => Company::inRandomOrder()->first()->id,
            'table_name' => $tableName,
            'table_type' => fake()->randomElement(['Dynamic', 'Fixed'])
        ];
    }
}
