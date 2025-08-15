<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Models\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum'); 
    }

    public function test_can_create_translation_with_values()
    {
        $this->authenticateUser();

        $locale1 = Locale::factory()->create();
        $locale2 = Locale::factory()->create();

        $payload = [
            'key' => 'login_button',
            'namespace' => 'app',
            'description' => 'Login button text',
            'values' => [
                ['locale_id' => $locale1->id, 'content' => 'Login'],
                ['locale_id' => $locale2->id, 'content' => 'Connexion']
            ]
        ];

        $response = $this->postJson('/api/translations', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['key' => 'login_button']);
    }

    public function test_export_json_endpoint_returns_correct_structure()
    {
        $this->authenticateUser();

        $locale = Locale::factory()->create(['code' => 'en']);
        $translationKey = TranslationKey::factory()->create(['key' => 'login_button']);
        TranslationValue::factory()->create([
            'translation_key_id' => $translationKey->id,
            'locale_id' => $locale->id,
            'value' => 'Login'
        ]);

        $response = $this->getJson('/api/translations/export/json');

        $response->assertStatus(200)
                 ->assertJsonStructure(['en' => ['login_button']]);
    }

    public function test_export_json_endpoint_performance()
    {
        $this->authenticateUser();

        $locales = Locale::factory()->count(5)->create();
        $keys = TranslationKey::factory()->count(1000)->create();

        foreach ($keys as $key) {
            foreach ($locales as $locale) {
                TranslationValue::factory()->create([
                    'translation_key_id' => $key->id,
                    'locale_id' => $locale->id,
                ]);
            }
        }

        $start = microtime(true);

        $response = $this->getJson('/api/translations/export/json');

        $end = microtime(true);
        $duration = $end - $start;

        $this->assertLessThan(0.5, $duration, "Export endpoint took {$duration} seconds, should be < 0.5");
        $response->assertStatus(200);
    }
}
