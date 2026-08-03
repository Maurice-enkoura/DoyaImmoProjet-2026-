<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\FormuleAbonnementEnum;
use Illuminate\Validation\Rule;

class AbonnementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'formule' => ['required', Rule::enum(FormuleAbonnementEnum::class)],
        ];
    }
}