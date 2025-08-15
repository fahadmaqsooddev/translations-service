<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocaleRequest;
use App\Http\Resources\LocaleResource;
use App\Models\Locale;

class LocaleController extends Controller
{
    
    public function index()
    {
        $locales = Locale::select('id', 'code', 'name')->get();
        return LocaleResource::collection($locales);
    }

    public function store(LocaleRequest $request): LocaleResource
    {
        $locale = Locale::create($request->validated());

        return new LocaleResource($locale);
    }

    public function show(Locale $locale): LocaleResource
    {
        return new LocaleResource($locale);
    }

    public function update(LocaleRequest $request, Locale $locale): LocaleResource
    {
        $locale->update($request->validated());

        return new LocaleResource($locale);
    }

    public function destroy(Locale $locale): array
    {
        $locale->delete();
        return ['message' => 'Locale deleted successfully'];
    }
}
