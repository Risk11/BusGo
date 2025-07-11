<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Cari jadwal berdasarkan rute dan tanggal.
     */
    public function search(Request $request)
    {
        $request->validate([
            'asal' => 'required|string',
            'tujuan' => 'required|string',
            'tanggal_berangkat' => 'required|date'
        ]);

        $jadwals = Jadwal::with(['bus', 'kursis' => function ($query) {
                $query->where('status', 'Tersedia');
            }])
            ->where('asal', 'like', '%' . $request->asal . '%')
            ->where('tujuan', 'like', '%' . $request->tujuan . '%')
            ->where('tanggal_berangkat', $request->tanggal_berangkat)
            ->where('tanggal_berangkat', '>=', Carbon::today()->toDateString())
            ->get()
            ->map(function ($jadwal) {
                // Menambahkan atribut ketersediaan kursi
                $jadwal->kursi_tersedia = $jadwal->kursis->count();
                return $jadwal;
            });

        if ($jadwals->isEmpty()) {
            return response()->json(['message' => 'Jadwal tidak ditemukan'], 404);
        }

        return response()->json($jadwals);
    }

    /**
     * Menampilkan detail spesifik jadwal termasuk layout kursi.
     */
    public function show($id)
    {
        $jadwal = Jadwal::with(['bus', 'kursis'])->findOrFail($id);
        return response()->json($jadwal);
    }
}
