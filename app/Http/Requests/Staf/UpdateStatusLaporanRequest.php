<?php

namespace App\Http\Requests\Staf;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusLaporanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status' => 'required|in:Dikirim,Ditindaklanjuti,Selesai',
            'catatan_admin' => 'nullable|string|max:2000',
        ];
    }
}
