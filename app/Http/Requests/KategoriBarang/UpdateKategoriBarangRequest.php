<?php

namespace App\Http\Requests\KategoriBarang;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['pengurus', 'petugas_persediaan']);
    }

    public function rules(): array
    {
        $kategoriId = $this->route('kategori')->id_kategori ?? $this->route('kategoriBarang')->id_kategori ?? null;

        return [
            'nama_kategori' => ['required', 'string', 'max:50', Rule::unique('kategori_barang', 'nama_kategori')->ignore($kategoriId, 'id_kategori')],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max' => 'Nama kategori maksimal 50 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
        ];
    }
}