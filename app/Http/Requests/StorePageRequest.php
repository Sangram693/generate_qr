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
            'row_number' => 'required|integer',
            'product_type' => 'required|in:w-beam,pole,high-mast',
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
            'row_number.required' => 'Row number is required',
            'row_number.integer' => 'Row number must be an integer',
            'product_type.required' => 'Product type is required',
            'product_type.in' => 'Product type must be one of the following: w-beam, pole, high-mast',
        ];
    }
}