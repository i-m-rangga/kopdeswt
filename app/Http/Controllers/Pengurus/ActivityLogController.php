<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pengguna;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->getFilters($request);
        $logs = $this->getLogs($filters);

        // Get users for filter dropdown
        $penggunas = Pengguna::orderBy('nama')->get(['id_pengguna', 'nama', 'role']);

        // Get distinct modules for filter
        $moduls = ActivityLog::distinct()->pluck('modul')->sort()->values();

        return view('pengurus.activity-log.index', compact('logs', 'filters', 'penggunas', 'moduls'));
    }

    protected function getFilters(Request $request): array
    {
        return [
            'pengguna_id' => $request->get('pengguna_id'),
            'modul' => $request->get('modul'),
            'aktivitas' => $request->get('aktivitas'),
            'tanggal_awal' => $request->get('tanggal_awal', now()->startOfMonth()->format('Y-m-d')),
            'tanggal_akhir' => $request->get('tanggal_akhir', now()->format('Y-m-d')),
        ];
    }

    protected function getLogs(array $filters)
    {
        $query = ActivityLog::query()
            ->with('pengguna')
            ->whereBetween('waktu', [$filters['tanggal_awal'] . ' 00:00:00', $filters['tanggal_akhir'] . ' 23:59:59']);

        if ($filters['pengguna_id']) {
            $query->where('id_pengguna', $filters['pengguna_id']);
        }

        if ($filters['modul']) {
            $query->where('modul', $filters['modul']);
        }

        if ($filters['aktivitas']) {
            $query->where('aktivitas', $filters['aktivitas']);
        }

        return $query->latest('waktu')
            ->paginate(20)
            ->withQueryString();
    }

    public function detail(Request $request)
    {
        $log = ActivityLog::with('pengguna')->findOrFail($request->get('detail'));

        $diff = $log->data_diff;
        $html = '<dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">';
        $html .= '<div><dt class="text-sm font-medium text-gray-500">ID Log</dt><dd class="mt-1 text-lg font-mono text-gray-900">#' . $log->id_log . '</dd></div>';
        $html .= '<div><dt class="text-sm font-medium text-gray-500">Waktu</dt><dd class="mt-1 text-lg text-gray-900">' . $log->waktu->format('d F Y H:i:s') . '</dd></div>';
        $html .= '<div><dt class="text-sm font-medium text-gray-500">Pengguna</dt><dd class="mt-1 text-lg text-gray-900">' . $log->pengguna->nama . ' (' . ucfirst($log->pengguna->role) . ')' . '</dd></div>';
        $html .= '<div><dt class="text-sm font-medium text-gray-500">Aktivitas</dt><dd class="mt-1 text-lg">';
        $html .= '<span class="px-2 py-1 rounded-full text-xs font-medium ' . ($log->aktivitas === 'create' ? 'bg-green-100 text-green-700' : ($log->aktivitas === 'update' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) . '">' . $log->aktivitas_label . '</span>';
        $html .= '</dd></div>';
        $html .= '<div><dt class="text-sm font-medium text-gray-500">Modul</dt><dd class="mt-1 text-lg">';
        $html .= '<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">' . $log->modul_label . '</span>';
        $html .= '</dd></div>';
        $html .= '</dl>';

        if ($diff && ($diff['old'] || $diff['new'])) {
            $html .= '<div class="mt-6 border-t border-gray-200 pt-6">';
            $html .= '<h4 class="text-lg font-medium text-gray-900 mb-4">Perubahan Data</h4>';
            $html .= '<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">';

            // Old data
            $html .= '<div>';
            $html .= '<h5 class="text-sm font-medium text-red-600 mb-2">Data Sebelum (Old)</h5>';
            $html .= '<div class="bg-red-50 rounded-lg p-4 max-h-64 overflow-y-auto">';
            if ($diff['old']) {
                $html .= '<pre class="text-sm text-gray-900 whitespace-pre-wrap">' . json_encode($diff['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            } else {
                $html .= '<p class="text-sm text-gray-500">Tidak ada data lama (create baru)</p>';
            }
            $html .= '</div></div>';

            // New data
            $html .= '<div>';
            $html .= '<h5 class="text-sm font-medium text-green-600 mb-2">Data Sesudah (New)</h5>';
            $html .= '<div class="bg-green-50 rounded-lg p-4 max-h-64 overflow-y-auto">';
            if ($diff['new']) {
                $html .= '<pre class="text-sm text-gray-900 whitespace-pre-wrap">' . json_encode($diff['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            } else {
                $html .= '<p class="text-sm text-gray-500">Tidak ada data baru (delete)</p>';
            }
            $html .= '</div></div>';

            $html .= '</div></div>';
        }

        return response()->json(['html' => $html]);
    }
}