<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locale;
use App\Models\Tag;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    
        User::factory()->count(50)->create();

        // 2. Locales
        $locales = Locale::factory()->count(5)->create();

        // 3. Tags
        $tags = Tag::factory()->count(10)->create();

        TranslationKey::factory()
            ->count(1000)
            ->withTags(2)
            ->create()
            ->each(function ($key) use ($locales) {
                // Prepare values for batch insert
                $values = $locales->map(fn($locale) => [
                    'translation_key_id' => $key->id,
                    'locale_id' => $locale->id,
                    'value' => fake()->sentence(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();

                TranslationValue::insert($values);
            });
    }
}
