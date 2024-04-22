<?php

declare(strict_types=1);

namespace ThijsSchalk\LaravelDismissibles\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ThijsSchalk\LaravelDismissibles\Models\Dismissal;
use ThijsSchalk\LaravelDismissibles\Models\Dismisser;
use ThijsSchalk\LaravelDismissibles\Models\Dismissible;

class DismissalFactory extends Factory
{
    protected $model = Dismissal::class;

    public function definition(): array
    {
        return [
            'dismisser_id'    => fn () => Dismisser::factory()->create(),
            'dismisser_type'  => Dismisser::class,
            'dismissible_id'  => fn () => Dismissible::factory()->create(),
            'dismissed_until' => function (array $attributes) {
                if ($this->faker->optional()) {
                    return null;
                }

                $dismissible = Dismissible::find($attributes['dismissible_id']);

                return $this->faker->dateTimeBetween($dismissible->active_from, $dismissible->active_until);
            },
            'extra_data' => $this->faker->optional() ? ['some_extra_data' => $this->faker->randomNumber()] : null,
        ];
    }
}
