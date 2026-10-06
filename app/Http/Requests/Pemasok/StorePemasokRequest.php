<?php

namespace App\Http\Requests\Pemasok;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePemasokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'pengurus';
    }

    public function rules(): array
    {
        return [
            'nama_pemasok' => ['required', 'string', 'max:100', 'unique:pemasok,nama_pemasok'],
            'kontak' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pemasok.required' => 'Nama pemasok wajib diisi.',
            'nama_pemasok.max' => 'Nama pemasok maksimal 100 karakter.',
            'nama_pemasok.unique' => 'Nama pemasok sudah digunakan.',
            'kontak.max' => 'Kontak maksimal 50 karakter.',
            'alamat.max' => 'Alamat maksimal 150 karakter.',
        ];
    }
}