<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanPublikRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kategori'           => 'required|in:infrastruktur,kamtibmas,umum',
            'nama_pelapor'       => 'required|string|max:255',
            'alamat_pelapor'     => 'required|string|max:500',
            'kontak_pelapor'     => 'required|string|max:20',
            'deskripsi'          => 'required|string|max:5000',
            'lokasi_kejadian'    => 'required|string|max:500',
            'lokasi_gps'         => 'nullable|string|max:100',
            'cf_turnstile_token' => config('services.turnstile.enabled') ? 'required|string' : 'nullable|string',
            'lampiran'           => 'nullable|array|max:5',
            'lampiran.*'         => 'file|mimes:jpg,jpeg,png,mp4,pdf|max:10240',
        ];
    }
}
