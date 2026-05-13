<?php

namespace App\Http\Requests\Warga;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanWargaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kategori' => 'required|in:infrastruktur,kamtibmas,umum',
            'deskripsi' => 'required|string|max:5000',
            'lokasi_kejadian' => 'required|string|max:500',
            'lokasi_gps' => 'nullable|string|max:100',
            'lampiran' => 'nullable|array|max:5',
            'lampiran.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }
}
