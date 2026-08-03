<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\MotifSignalementEnum;
use Illuminate\Validation\Rule;

class SignalementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signalable_id' => 'required|integer',
            'signalable_type' => 'required|string|in:App\Models\BienImmobilier,App\Models\Proposition,App\Models\DemandeImmobiliere',
            'motif' => ['required', Rule::enum(MotifSignalementEnum::class)],
            'description' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'description.min' => 'La description doit contenir au moins 10 caractères.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',
        ];
    }
}