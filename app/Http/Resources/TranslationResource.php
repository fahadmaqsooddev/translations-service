<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'values' => $this->values->map(function ($value) {
                return [
                    'locale' => $value->locale->code,
                    'value' => $value->value,
                ];
            }),
            'tags' => $this->tags->pluck('name'),
        ];
    }
}
