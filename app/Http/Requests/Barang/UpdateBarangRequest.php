<?php

namespace App\Http\Requests\Barang;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['pengurus', 'petugas_persediaan']);
    }

    public function rules(): array
    {
        $barangId = $this->route('barang')->id_barang ?? $this->route('barangId') ?? null;

        return [
            'id_kategori' => ['required', 'integer', 'exists:kategori_barang,id_kategori'],
            'nama_barang' => ['required', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:20'],
            'harga' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stok' => ['nullable', 'integer', 'min:0'],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_kategori.required' => 'Kategori wajib dipilih.',
            'id_kategori.exists' => 'Kategori tidak valid.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.max' => 'Nama barang maksimal 100 karakter.',
            'satuan.required' => 'Satuan wajib diisi.',
            'satuan.max' => 'Satuan maksimal 20 karakter.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh negatif.',
            'stok.min' => 'Stok tidak boleh negatif.',
            'stok_minimum.min' => 'Stok minimum tidak boleh negatif.',
        ];
    }
}