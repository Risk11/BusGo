<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tiket;
use App\Models\Pembayaran;
use Illuminate\Support\Str;

class PembayaranController extends Controller
{
    // Frontend memanggil ini setelah booking berhasil
    public function initiatePayment(Request $request)
    {
        $request->validate(['id_tiket' => 'required|exists:tikets,id_tiket']);
        $tiket = Tiket::with('jadwal')->findOrFail($request->id_tiket);

        // Pastikan tiket belum lunas
        if ($tiket->status_pembayaran !== 'Belum Bayar') {
            return response()->json(['message' => 'Tiket ini tidak bisa diproses pembayarannya.'], 409);
        }

        // --- START MOCK PAYMENT GATEWAY ---
        // Di dunia nyata, di sini Anda akan memanggil API payment gateway
        // $midtrans = new Midtrans();
        // $transaction = $midtrans->createTransaction(...);
        // $paymentUrl = $transaction->redirect_url;

        // Mocking: kita anggap gateway mengembalikan ID transaksi unik
        $mockTransactionId = 'MOCKTRANS-' . strtoupper(Str::random(10));
        $mockPaymentUrl = url('/mock-payment-page/' . $mockTransactionId);
        // --- END MOCK PAYMENT GATEWAY ---

        // Buat record pembayaran di database kita
        $pembayaran = Pembayaran::create([
            'id_tiket' => $tiket->id_tiket,
            'metode' => 'MOCK_GATEWAY',
            'jumlah_bayar' => $tiket->jadwal->harga,
            'status' => 'Pending',
            'kode_transaksi_gateway' => $mockTransactionId,
        ]);

        return response()->json([
            'message' => 'Silakan lanjutkan pembayaran.',
            'payment_url' => $mockPaymentUrl, // Kirim URL ini ke frontend
            'transaction_id' => $pembayaran->id_pembayaran
        ]);
    }

    // Payment gateway akan mengirim POST request ke endpoint ini (webhook)
    public function handleCallback(Request $request)
    {
        // Di dunia nyata, validasi signature dari gateway sangat penting!
        // $signatureKey = $request->header('x-callback-token');
        // if(!$this->isValidSignature($request->all(), $signatureKey)) {
        //   return response()->json(['message' => 'Invalid signature'], 403);
        // }

        $transactionStatus = $request->input('transaction_status'); // e.g., 'capture', 'settlement', 'deny'
        $gatewayTransactionId = $request->input('kode_transaksi_gateway');

        $pembayaran = Pembayaran::where('kode_transaksi_gateway', $gatewayTransactionId)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        // Jika pembayaran sukses
        if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
            $pembayaran->status = 'Sukses';
            $pembayaran->waktu_bayar = now();

            $tiket = $pembayaran->tiket;
            $tiket->status_pembayaran = 'Lunas';
            $tiket->save();

            // Kirim notifikasi ke user (Email, Push Notification)
            // event(new PembayaranSukses($tiket));
        } else {
            // Handle status gagal atau lainnya
            $pembayaran->status = 'Gagal';

            $tiket = $pembayaran->tiket;
            $tiket->status_pembayaran = 'Gagal';
            $tiket->save();
        }

        $pembayaran->save();

        // Beri response OK ke payment gateway
        return response()->json(['message' => 'Callback received successfully']);
    }
}
