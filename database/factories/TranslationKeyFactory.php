<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TranslationKey;
use App\Models\Tag;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TranslationKey>
 */
class TranslationKeyFactory extends Factory
{
    protected $model = TranslationKey::class;

    public function definition(): array
    {
        return [
            'namespace'   => $this->faker->randomElement(['auth', 'home', 'profile', 'settings']),
            'key'         => $this->faker->unique()->slug(3),
            'description' => $this->faker->sentence(),
        ];
    }

    public function withTags(int $count = 1)
    {
        return $this->afterCreating(function (TranslationKey $key) use ($count) {
            $tags = Tag::inRandomOrder()->take($count)->get();
            if ($tags->count() === 0) {
                $tags = Tag::factory($count)->create();
            }
            $key->tags()->attach($tags);
        });
    }
}
