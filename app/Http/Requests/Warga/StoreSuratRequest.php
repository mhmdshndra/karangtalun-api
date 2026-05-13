<?php

namespace App\Http\Requests\Warga;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuratRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'jenis_surat' => 'required|in:surat_keterangan_domisili,surat_keterangan_tidak_mampu,surat_pengantar_skck,surat_keterangan_usaha',
            'pemohon_nik' => 'required|string|size:16|exists:anggota_kk,nik',
            'keperluan' => 'required|string|max:2000',
            'berkas' => 'nullable|array|max:5',
            'berkas.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_surat.required' => 'Jenis surat wajib dipilih.',
            'pemohon_nik.required' => 'NIK pemohon wajib diisi.',
            'pemohon_nik.size' => 'NIK harus 16 digit.',
            'pemohon_nik.exists' => 'NIK pemohon tidak terdaftar di database kependudukan.',
            'keperluan.required' => 'Keperluan wajib diisi.',
        ];
    }
}
