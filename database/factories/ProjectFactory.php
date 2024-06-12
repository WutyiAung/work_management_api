<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = implode(' ', fake()->words(3));
        $name = ucwords($name);
        $startDate = fake()->dateTimeBetween('-1 years', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+1 years');
        return [
            'name' => $name,
            'customer_id' => Customer::inRandomOrder()->first()->id ?? Customer::factory()->create()->id,
            'description' => fake()->paragraph(),
            'value' => fake()->randomNumber(5),
            'contract_date' => fake()->date(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'document' => null,
            'user_id' => User::inRandomOrder()->first()->id
        ];
    }
}
