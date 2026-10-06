@extends('layouts.pengurus')

@section('title', 'Riwayat Aktivitas (Audit Trail)')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Riwayat Aktivitas (Audit Trail)</h2>
        <p class="text-gray-600">Lacak semua aksi tambah, edit, hapus di sistem</p>
    </div>
</div>

<!-- Filter Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('pengurus.activity-log.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div>
            <label for="tanggal_awal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" id="tanggal_awal"
                   value="{{ $filters['tanggal_awal'] }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                   value="{{ $filters['tanggal_akhir'] }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="pengguna_id" class="block text-sm font-medium text-gray-700 mb-1">Pengguna</label>
            <select name="pengguna_id" id="pengguna_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Pengguna</option>
                @foreach($penggunas as $pengguna)
                    <option value="{{ $pengguna->id_pengguna }}" {{ $filters['pengguna_id'] == $pengguna->id_pengguna ? 'selected' : '' }}>
                        {{ $pengguna->nama }} ({{ ucfirst($pengguna->role) }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="modul" class="block text-sm font-medium text-gray-700 mb-1">Modul</label>
            <select name="modul" id="modul"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Modul</option>
                @foreach($moduls as $modul)
                    <option value="{{ $modul }}" {{ $filters['modul'] == $modul ? 'selected' : '' }}>
                        {{ \App\Models\ActivityLog::getModulLabelStatic($modul) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="aktivitas" class="block text-sm font-medium text-gray-700 mb-1">Aktivitas</label>
            <select name="aktivitas" id="aktivitas"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Aktivitas</option>
                <option value="create" {{ $filters['aktivitas'] == 'create' ? 'selected' : '' }}>Tambah</option>
                <option value="update" {{ $filters['aktivitas'] == 'update' ? 'selected' : '' }}>Edit</option>
                <option value="delete" {{ $filters['aktivitas'] == 'delete' ? 'selected' : '' }}>Hapus</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                <i class="fas fa-search mr-2"></i> Filter
            </button>
            <a href="{{ route('pengurus.activity-log.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition inline-flex items-center">
                <i class="fas fa-undo mr-2"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    @if($logs->isEmpty())
        <div class="p-12 text-center text-gray-500">
            <i class="fas fa-history text-4xl mb-2"></i>
            <p>Tidak ada riwayat aktivitas untuk filter ini</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Diproses</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-900">
                            {{ $log->waktu->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $log->pengguna->nama }}</div>
                            <div class="text-xs text-gray-500">{{ ucfirst($log->pengguna->role) }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($log->aktivitas === 'create')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">{{ $log->aktivitas_label }}</span>
                            @elseif($log->aktivitas === 'update')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">{{ $log->aktivitas_label }}</span>
                            @elseif($log->aktivitas === 'delete')
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-medium">{{ $log->aktivitas_label }}</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-medium">{{ $log->aktivitas_label }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                                {{ $log->modul_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button type="button"
                                    class="text-blue-600 hover:text-blue-900 text-sm underline"
                                    onclick="showDetail({{ $log->id_log }})">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    @endif
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[80vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Detail Aktivitas</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto" id="modalContent">
            <!-- Content loaded via JS -->
        </div>
    </div>
</div>

@section('scripts')
<script>
    function showDetail(logId) {
        const modal = document.getElementById('detailModal');
        const content = document.getElementById('modalContent');
        
        // Show loading
        content.innerHTML = '<div class="flex justify-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Fetch detail
        fetch(`{{ route('pengurus.activity-log.detail') }}?detail=${logId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                content.innerHTML = data.html;
            } else {
                content.innerHTML = '<p class="text-red-500">Gagal memuat detail</p>';
            }
        })
        .catch(() => {
            content.innerHTML = '<p class="text-red-500">Terjadi kesalahan</p>';
        });
    }

    function closeModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close on backdrop click
    document.getElementById('detailModal').addEventListener('click', (e) => {
        if (e.target.id === 'detailModal') {
            closeModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endsection