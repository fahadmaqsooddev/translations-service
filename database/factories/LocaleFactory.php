<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Locale;

class LocaleFactory extends Factory
{
    protected $model = Locale::class;

    public function definition(): array
    {
        $code = $this->faker->unique()->languageCode();
        return [
            'code' => $code,
            'name' => ucfirst($this->faker->languageCode()) . " language",
        ];
    }
}
