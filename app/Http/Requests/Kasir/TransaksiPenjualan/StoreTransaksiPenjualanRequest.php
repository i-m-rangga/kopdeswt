<?php

namespace App\Http\Requests\Kasir\TransaksiPenjualan;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiPenjualanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'kasir';
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_barang' => ['required', 'integer', 'exists:barang,id_barang'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
            'metode_pembayaran' => ['required', 'in:tunai,non-tunai'],
            'total_bayar' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
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
            'items.*.harga_satuan.required' => 'Harga satuan wajib diisi.',
            'items.*.harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
            'items.*.harga_satuan.min' => 'Harga satuan tidak boleh negatif.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran tidak valid.',
            'total_bayar.required' => 'Total bayar wajib diisi.',
            'total_bayar.numeric' => 'Total bayar harus berupa angka.',
            'total_bayar.min' => 'Total bayar tidak boleh negatif.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(fn($item) => !empty($item['id_barang']) && !empty($item['jumlah']))
            ->values()
            ->toArray();

        $this->merge(['items' => $items]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            $metodePembayaran = $this->input('metode_pembayaran');
            $totalBayar = (float) $this->input('total_bayar', 0);

            // Hitung total transaksi
            $totalTransaksi = collect($items)->sum(fn($item) => (float) $item['harga_satuan'] * (int) $item['jumlah']);

            // Validasi stok cukup
            foreach ($items as $index => $item) {
                $barang = \App\Models\Barang::find($item['id_barang']);
                if ($barang && $barang->stok < (int) $item['jumlah']) {
                    $validator->errors()->add("items.{$index}.jumlah", "Stok {$barang->nama_barang} tidak mencukupi (tersedia: {$barang->stok} {$barang->satuan}).");
                }
            }

            // Validasi pembayaran tunai
            if ($metodePembayaran === 'tunai' && $totalBayar < $totalTransaksi) {
                $validator->errors()->add('total_bayar', 'Uang diterima tidak boleh kurang dari total transaksi.');
            }

            // Validasi pembayaran non-tunai
            if ($metodePembayaran === 'non-tunai' && $totalBayar != $totalTransaksi) {
                $validator->errors()->add('total_bayar', 'Total bayar non-tunai harus sama dengan total transaksi.');
            }
        });
    }
}