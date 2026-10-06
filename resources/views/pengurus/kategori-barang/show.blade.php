@extends('layouts.pengurus')

@section('title', 'Detail Kategori Barang')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Detail Kategori Barang</h2>
        <p class="text-gray-600">Informasi lengkap kategori: {{ $kategoriBarang->nama_kategori }}</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('pengurus.kategori-barang.edit', $kategoriBarang) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <a href="{{ route('pengurus.kategori-barang.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
        <div>
            <dt class="text-sm font-medium text-gray-500">Nama Kategori</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $kategoriBarang->nama_kategori }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Jumlah Barang</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $kategoriBarang->barang_count ?? 0 }} barang</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $kategoriBarang->created_at->format('d F Y H:i') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $kategoriBarang->updated_at->format('d F Y H:i') }}</dd>
        </div>
    </dl>
</div>
@endsection