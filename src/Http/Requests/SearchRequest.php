<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $from
 * @property-read string $to
 * @property-read string $date
 */
class SearchRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
            'date' => 'required|date',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return (array) trans('wbeng-client::validation.attributes');
    }
}
