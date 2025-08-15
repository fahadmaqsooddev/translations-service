<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class TranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $translationId = $this->route('translation')?->id ?? null;

        return [
            'key' => [
                'required_without:values',
                'string',
                Rule::unique('translation_keys')
                    ->where(function ($query) {
                        return $query->where('namespace', $this->namespace);
                    })
                    ->ignore($translationId)
            ],
            'namespace' => 'nullable|string',
            'description' => 'nullable|string',
            'values' => 'sometimes|array',
            'values.*.locale_id' => 'required_with:values|exists:locales,id',
            'values.*.content' => 'required_with:values|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('tags')) {
            $this->merge(['tags' => array_filter($this->tags)]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
