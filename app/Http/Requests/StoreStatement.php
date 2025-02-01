<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatement extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
//        return [
//            'statement' => 'required|mimetypes:application/pdf,text/csv|max:10240'
//        ];

        return [
            'statement' => ['required', 'file', 'mimetypes:application/pdf,text/csv,application/zip', 'max:51200'], // 50MB max for zip
        ];
    }
}
