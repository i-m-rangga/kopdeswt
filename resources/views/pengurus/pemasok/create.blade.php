@extends('layouts.pengurus')

@section('title', 'Tambah Pemasok')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Tambah Pemasok</h2>
    <p class="text-gray-600">Isi formulir di bawah untuk menambah pemasok baru</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
    <form method="POST" action="{{ route('pengurus.pemasok.store') }}" class="space-y-6">
        @csrf

        <div>
            <label for="nama_pemasok" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemasok <span class="text-red-500">*</span></label>
            <input type="text" name="nama_pemasok" id="nama_pemasok"
                   value="{{ old('nama_pemasok') }}"
                   required maxlength="100"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('nama_pemasok')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kontak" class="block text-sm font-medium text-gray-700 mb-1">Kontak</label>
            <input type="text" name="kontak" id="kontak"
                   value="{{ old('kontak') }}"
                   maxlength="50"
                   placeholder="Nomor telepon / email"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('kontak')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <textarea name="alamat" id="alamat" rows="3" maxlength="150"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('alamat') }}</textarea>
            @error('alamat')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('pengurus.pemasok.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection