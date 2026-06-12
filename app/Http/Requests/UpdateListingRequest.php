<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\JobTags;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'tags' => ['sometimes', Rule::enum(JobTags::class)],
            'company' => ['sometimes', 'string', 'max:255'],
            'location' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'website' => ['sometimes', 'url', 'max:255'],
            'description' => ['sometimes', 'string'],
            'date' => ['sometimes', 'date', 'date_format:Y-m-d'],
        ];
    }
}
