<?php

namespace Database\Factories;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition()
    {
        // try to pick a random existing plan id if available
        $planId = null;
        try {
            $planId = \App\Models\Plan::inRandomOrder()->value('id');
        } catch (\Throwable $e) {
            $planId = null;
        }

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), // default password
            'subscription_status' => $this->faker->randomElement([0, 1]),
            'subscription_id' => $planId,
            'sub_date' => $this->faker->date(),
            'next_due_date' => $this->faker->dateTimeBetween('+1 week', '+1 year')->format('Y-m-d'),
            'is_flagged' => $this->faker->boolean(10),
            'country' => $this->faker->country(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
