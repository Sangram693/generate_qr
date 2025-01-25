<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page_height' => 'required|string',
            'page_width' => 'required|string',
            'margin_top' => 'required|string',
            'margin_bottom' => 'required|string',
            'margin_left' => 'required|string',
            'margin_right' => 'required|string',
            'qr_height' => 'required|string',
            'qr_width' => 'required|string',
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */

    public function messages(): array
    {
        return [
            'page_height.required' => 'Page height is required',
            'page_width.required' => 'Page width is required',
            'margin_top.required' => 'Margin top is required',
            'margin_bottom.required' => 'Margin bottom is required',
            'margin_left.required' => 'Margin left is required',
            'margin_right.required' => 'Margin right is required',
            'qr_height.required' => 'QR height is required',
            'qr_width.required' => 'QR width is required',
            'excel_file.required' => 'Excel file is required',
            'excel_file.mimes' => 'Excel file must be of type xlsx or xls',
        ];
    }
}
