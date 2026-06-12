<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\JobTags;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'tags' => ['required', Rule::enum(JobTags::class)],
            'company' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['required', 'url', 'max:255'],
            'description' => ['required', 'string'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
        ];
    }
}
