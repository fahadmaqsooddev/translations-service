<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Models\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_translation_key_can_be_created()
    {
        $key = TranslationKey::factory()->create([
            'namespace' => 'app',
            'key' => 'login_button',
            'description' => 'Login button text'
        ]);

        $this->assertDatabaseHas('translation_keys', [
            'key' => 'login_button',
            'namespace' => 'app'
        ]);
    }

    public function test_translation_value_belongs_to_translation_key_and_locale()
    {
        $locale = Locale::factory()->create();
        $translationKey = TranslationKey::factory()->create();

        $value = TranslationValue::factory()->create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Login'
        ]);

        $this->assertEquals($translationKey->id, $value->translationKey->id);
        $this->assertEquals($locale->id, $value->locale->id);
    }
}
