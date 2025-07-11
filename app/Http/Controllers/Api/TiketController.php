<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Tiket;
use App\Models\Jadwal;
use App\Models\Kursi;
use Illuminate\Support\Facades\DB;

class TiketController extends Controller
{
    /**
     * Membuat pesanan tiket baru.
     */
    public function book(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwals,id_jadwal',
            'nomor_kursi' => 'required|array',
            'nomor_kursi.*' => 'required|string',
        ]);

        $user = Auth::user();
        $penumpang = $user->penumpang;
        $id_jadwal = $request->id_jadwal;
        $kursi_dipesan = $request->nomor_kursi;

        // Gunakan transaksi untuk memastikan integritas data
        return DB::transaction(function () use ($penumpang, $id_jadwal, $kursi_dipesan) {

            // 1. Cek ketersediaan kursi
            $kursiTersedia = Kursi::where('id_jadwal', $id_jadwal)
                ->whereIn('nomor_kursi', $kursi_dipesan)
                ->where('status', 'Tersedia')
                ->lockForUpdate() // Kunci baris untuk mencegah race condition
                ->get();

            if (count($kursiTersedia) !== count($kursi_dipesan)) {
                return response()->json(['message' => 'Satu atau lebih kursi tidak tersedia.'], 409);
            }

            // 2. Buat tiket untuk setiap kursi
            $tikets = [];
            foreach($kursi_dipesan as $nomor) {
                $tiket = Tiket::create([
                    'kode_booking' => 'BOOK-' . strtoupper(Str::random(8)),
                    'id_penumpang' => $penumpang->id_penumpang,
                    'id_jadwal' => $id_jadwal,
                    'nomor_kursi' => $nomor,
                    'status_pembayaran' => 'Belum Bayar',
                ]);
                array_push($tikets, $tiket);
            }

            // 3. Update status kursi menjadi 'Dipesan'
            Kursi::whereIn('id_kursi', $kursiTersedia->pluck('id_kursi'))->update(['status' => 'Dipesan']);

            // Di sini Anda akan mengarahkan ke proses pembayaran
            // Misalnya, membuat entri di tabel `pembayarans` dan mengembalikan URL pembayaran

            return response()->json([
                'message' => 'Pemesanan berhasil dibuat. Silakan lanjutkan ke pembayaran.',
                'tikets' => $tikets,
            ], 201);
        });
    }

    /**
     * Melihat riwayat pemesanan pengguna.
     */
     public function history(Request $request)
     {
         $penumpangId = $request->user()->penumpang->id_penumpang;
         $tikets = Tiket::with('jadwal.bus', 'pembayaran')
                       ->where('id_penumpang', $penumpangId)
                       ->orderBy('created_at', 'desc')
                       ->get();

         return response()->json($tikets);
     }
}
