<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TranslationValue;
use App\Models\TranslationKey;
use App\Models\Locale;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TranslationValue>
 */
class TranslationValueFactory extends Factory
{
    protected $model = TranslationValue::class;

    public function definition(): array
    {
        return [
            'translation_key_id' => TranslationKey::factory(),
            'locale_id'          => Locale::inRandomOrder()->first()->id ?? Locale::factory(),
            'value'              => $this->faker->sentence(),
        ];
    }
}
