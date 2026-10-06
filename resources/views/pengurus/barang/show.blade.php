@extends('layouts.pengurus')

@section('title', 'Detail Barang')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Detail Barang</h2>
        <p class="text-gray-600">Informasi lengkap: {{ $barang->nama_barang }}</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('pengurus.barang.edit', $barang) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <a href="{{ route('pengurus.barang.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl">
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
        <div>
            <dt class="text-sm font-medium text-gray-500">Nama Barang</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $barang->nama_barang }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Kategori</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $barang->kategori->nama_kategori ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Satuan</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $barang->satuan }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Harga Jual</dt>
            <dd class="mt-1 text-lg text-gray-900">Rp {{ number_format($barang->harga, 0, ',', '.') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Stok Saat Ini</dt>
            <dd class="mt-1 text-lg {{ $barang->isStokRendah() ? 'text-red-600 font-bold' : 'text-gray-900' }}">{{ $barang->stok }} {{ $barang->satuan }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Stok Minimum</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $barang->stok_minimum }} {{ $barang->satuan }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Status Stok</dt>
            <dd class="mt-1 text-lg">
                @if($barang->isStokRendah())
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">STOK RENDAH</span>
                @else
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">NORMAL</span>
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $barang->created_at->format('d F Y H:i') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $barang->updated_at->format('d F Y H:i') }}</dd>
        </div>
    </dl>
</div>
@endsection