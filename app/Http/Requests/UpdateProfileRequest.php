<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nama_lengkap' => 'sometimes|string|max:255',
            'email'        => 'sometimes|nullable|email|max:255',
            'telepon'      => 'sometimes|nullable|string|max:20',
            'alamat'       => 'sometimes|nullable|string|max:1000',
            'rt_rw'        => 'sometimes|nullable|string|max:20',
        ];
    }
}
