<?php

namespace App\Http\Requests\Staf;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusPermohonanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status' => 'required|in:Dikirim,Diproses,Dijawab,Ditolak',
            'catatan_admin' => 'nullable|string|max:2000',
            'file_balasan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ];
    }
}
