<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RubikRequest extends FormRequest
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
        return [
            'name' => 'required|max:30',
            'email' => 'required|email|unique:clients',
            'password' => 'required|min:8|max:20|unique:clients'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'انتخاب اسم الزامیست',
            'name.max' => 'حداکثر کاراکتر 30 عدد میباشد',
            'email.required' => 'ایمیل خود را وارد کنید',
            'email.email' => 'لطفا فرمت ایمیل را رعایت کنید',
            'email.unique' => 'ایمیل وارد شده قبلا ثبت نام کرده است',
            'password.required' => 'رمز خود را وارد کنید',
            'password.min' => 'رمز باید حداقل 8 کاراکتر باشد',
            'password.max' => 'رمز باید حداکثر 20 کاراکتر باشد',
            'password.unique' => 'رمز وارد شده از امنیت بالایی برخوردار نمیباشد یا قبلا مورد استفاده قرار گرفته است'
        ];
    }
}
