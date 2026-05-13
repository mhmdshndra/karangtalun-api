<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StorePermohonanPublikRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nama_pemohon'       => 'required|string|max:255',
            'alamat_pemohon'     => 'required|string|max:1000',
            'kontak_pemohon'     => 'required|string|max:20',
            'tujuan_permohonan'  => 'required|string|max:2000',
            'informasi_diminta'  => 'required|string|max:2000',
            'cf_turnstile_token' => config('services.turnstile.enabled') ? 'required|string' : 'nullable|string',
            'lampiran'           => 'nullable|array|max:5',
            'lampiran.*'         => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ];
    }
}
