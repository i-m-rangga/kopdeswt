<?php

namespace App\Http\Requests\Petugas\PenerimaanBarang;

use Illuminate\Foundation\Http\FormRequest;

class StorePenerimaanBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'petugas_persediaan';
    }

    public function rules(): array
    {
        return [
            'id_pemasok' => ['required', 'integer', 'exists:pemasok,id_pemasok'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_barang' => ['required', 'integer', 'exists:barang,id_barang'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.harga_beli' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_pemasok.required' => 'Pemasok wajib dipilih.',
            'id_pemasok.exists' => 'Pemasok tidak valid.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
            'items.required' => 'Minimal satu barang wajib ditambahkan.',
            'items.array' => 'Data barang tidak valid.',
            'items.min' => 'Minimal satu barang wajib ditambahkan.',
            'items.*.id_barang.required' => 'Barang wajib dipilih.',
            'items.*.id_barang.exists' => 'Barang tidak valid.',
            'items.*.jumlah.required' => 'Jumlah wajib diisi.',
            'items.*.jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'items.*.jumlah.min' => 'Jumlah minimal 1.',
            'items.*.harga_beli.required' => 'Harga beli wajib diisi.',
            'items.*.harga_beli.numeric' => 'Harga beli harus berupa angka.',
            'items.*.harga_beli.min' => 'Harga beli tidak boleh negatif.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Filter out empty items
        $items = collect($this->input('items', []))
            ->filter(fn($item) => !empty($item['id_barang']) && !empty($item['jumlah']))
            ->values()
            ->toArray();

        $this->merge(['items' => $items]);
    }
}