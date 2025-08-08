<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'patient_id' => 1,
            'title' => $this->faker->text(10),
            'description' => $this->faker->paragraph(),
            'date' => now(),
            'status' => $this->faker->randomElement(['confirmed', 'done', 'canceled'])
        ];
    }
}
