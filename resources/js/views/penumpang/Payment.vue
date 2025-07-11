<template>
    <div class="container mx-auto p-8 max-w-lg">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-center mb-2">Simulasi Halaman Pembayaran</h2>
            <p class="text-center text-gray-500 mb-6">Ini adalah halaman tiruan dari payment gateway.</p>

            <div class="border-t border-b py-4 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">ID Transaksi</span>
                    <span class="font-mono">{{ transactionId }}</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-gray-600 font-semibold">Total Pembayaran</span>
                    <span class="font-bold text-xl text-blue-600">Rp {{ amount.toLocaleString('id-ID') }}</span>
                </div>
            </div>

            <p class="text-center text-sm mb-4">Klik tombol di bawah untuk mensimulasikan status pembayaran.</p>

            <div class="flex space-x-4">
                <button @click="simulatePayment('settlement')"
                    class="w-full bg-green-500 text-white py-3 rounded-lg font-bold hover:bg-green-600">
                    Simulasi Bayar (Sukses)
                </button>
                <button @click="simulatePayment('deny')"
                    class="w-full bg-red-500 text-white py-3 rounded-lg font-bold hover:bg-red-600">
                    Simulasi Bayar (Gagal)
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

// Di aplikasi nyata, data ini akan didapat dari API atau state management
const props = defineProps({
    bookingId: String // Seharusnya dari router
});

const router = useRouter();
const transactionId = ref("MOCKTRANS-ABC123"); // Mock ID
const amount = ref(150000); // Mock amount

const simulatePayment = async (status) => {
    try {
        // Ini adalah simulasi pemanggilan webhook dari gateway ke backend kita
        await axios.post('/api/payment/callback', {
            transaction_status: status,
            kode_transaksi_gateway: transactionId.value,
            // ... data lain dari gateway
        });

        alert(`Simulasi pembayaran ${status === 'settlement' ? 'SUKSES' : 'GAGAL'} telah dikirim.`);
        router.push({ name: 'BookingHistory' });

    } catch (error) {
        console.error("Gagal mensimulasikan callback:", error);
        alert("Terjadi kesalahan pada server.");
    }
};
</script>
