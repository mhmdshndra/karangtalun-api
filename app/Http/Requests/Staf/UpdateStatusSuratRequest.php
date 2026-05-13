<?php

namespace App\Http\Requests\Staf;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusSuratRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status' => 'required|in:Menunggu,Diproses,Selesai,Ditolak',
            'catatan_admin' => 'nullable|string|max:2000',
            'nomor_surat' => 'nullable|string|max:100',
        ];
    }
}
