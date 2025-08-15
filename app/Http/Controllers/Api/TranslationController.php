<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TranslationRequest;
use App\Http\Resources\TranslationResource;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


class TranslationController extends Controller
{
    /**
     * Display a paginated listing of translations.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 20);

        $translations = TranslationKey::with(['values.locale', 'tags'])
            ->paginate($perPage);

        return TranslationResource::collection($translations);
    }

    /**
     * Store a new translation key and its values.
     */
    public function store(TranslationRequest $request): TranslationResource
    {
        $translationKey = null;

        DB::transaction(function () use ($request, &$translationKey) {
            $data = $request->validated();

            $translationKey = TranslationKey::create(
                [
                    'key' => $data['key'],
                    'namespace' => $data['namespace'] ?? null,
                    'description' => $data['description'] ?? null
                ]
            );

            foreach ($data['values'] ?? [] as $value) {
                TranslationValue::create([
                    'translation_key_id' => $translationKey->id,
                    'locale_id' => $value['locale_id'],
                    'value' => $value['content'],
                ]);
            }

            if (!empty($data['tags'])) {
                $translationKey->tags()->attach($data['tags']);
            }
        });

        return new TranslationResource($translationKey);
    }

    /**
     * Display a single translation key with all values.
     */
    public function show(TranslationKey $translation): TranslationResource
    {
        $translation->load(['values.locale', 'tags']);
        return new TranslationResource($translation);
    }

    /**
     * Update a translation key and its values.
     */
    public function update(TranslationRequest $request, TranslationKey $translation): TranslationResource
    {
        DB::transaction(function () use ($request, $translation) {
            if ($request->filled('key')) {
                $translation->update(['key' => $request->key]);
            }

            foreach ($request->values ?? [] as $value) {
                TranslationValue::updateOrCreate(
                    [
                        'translation_key_id' => $translation->id,
                        'locale_id' => $value['locale_id'],
                    ],
                    ['content' => $value['content']]
                );
            }

            if ($request->has('tags')) {
                $translation->tags()->sync($request->tags);
            }
        });

        $translation->load(['values.locale', 'tags']);
        return new TranslationResource($translation);
    }

    /**
     * Delete a translation key along with its values and tags.
     */
    public function destroy(TranslationKey $translation): JsonResponse
    {
        DB::transaction(function () use ($translation) {
            $translation->values()->delete();
            $translation->tags()->detach();
            $translation->delete();
        });

        return response()->json(
            ['message' => 'Translation deleted successfully'],
            Response::HTTP_OK
        );
    }

    /**
     * Search translations by key, content, or tag.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $query = TranslationKey::query()->with(['values.locale', 'tags']);

        if ($request->filled('key')) {
            $query->where('key', 'like', '%' . $request->key . '%');
        }

        if ($request->filled('content')) {
            $query->whereHas('values', fn ($q) =>
                $q->where('content', 'like', '%' . $request->content . '%')
            );
        }

        if ($request->filled('tag_id')) {
            $query->whereHas('tags', fn ($q) =>
                $q->where('id', $request->tag_id)
            );
        }

        return TranslationResource::collection(
            $query->paginate($request->get('per_page', 20))
        );
    }

   public function exportJson(): JsonResponse
    {
        $translations = DB::table('translation_values')
            ->join('translation_keys', 'translation_values.translation_key_id', '=', 'translation_keys.id')
            ->join('locales', 'translation_values.locale_id', '=', 'locales.id')
            ->select('locales.code as locale', 'translation_keys.key', 'translation_values.value')
            ->orderBy('locales.code')
            ->orderBy('translation_keys.key')
            ->get()
            ->groupBy('locale') // group by locale code
            ->map(function ($items) {
                return $items->pluck('value', 'key');
            });

        return response()->json($translations, 200);
    }

}
