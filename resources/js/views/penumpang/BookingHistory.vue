<template>
    <div class="bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Riwayat Pemesanan Tiket</h1>

            <div v-if="loading" class="text-center">
                <p>Memuat riwayat...</p>
            </div>

            <div v-else-if="tickets.length === 0" class="text-center bg-white p-12 rounded-lg shadow">
                <p class="text-gray-500 text-lg">Anda belum memiliki riwayat pemesanan.</p>
                <router-link to="/"
                    class="mt-4 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">
                    Cari Tiket Sekarang
                </router-link>
            </div>

            <div v-else class="space-y-6">
                <div v-for="ticket in tickets" :key="ticket.id_tiket"
                    class="bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-1 transition-transform duration-300">
                    <div :class="getStatusColor(ticket.status_pembayaran)"
                        class="px-6 py-2 text-white font-bold text-sm">
                        Status: {{ ticket.status_pembayaran }}
                    </div>
                    <div class="p-6 md:flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Kode Booking: <span class="font-mono text-gray-800">{{
                                    ticket.kode_booking }}</span></p>
                            <h2 class="text-xl font-bold text-gray-900 mt-1">
                                {{ ticket.jadwal.asal }} &rarr; {{ ticket.jadwal.tujuan }}
                            </h2>
                            <p class="text-gray-600">{{ ticket.jadwal.bus.nama_bus }} - {{ ticket.jadwal.bus.kelas }}
                            </p>
                            <p class="text-gray-600">Kursi: <span class="font-semibold">{{ ticket.nomor_kursi }}</span>
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0 text-left md:text-right">
                            <p class="text-lg font-semibold text-gray-800">
                                {{ new Date(ticket.jadwal.tanggal_berangkat).toLocaleDateString('id-ID', {
                                    weekday:
                                        'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                            </p>
                            <p class="text-gray-600">Pukul {{ ticket.jadwal.jam_berangkat.substring(0, 5) }} WIB</p>

                            <div class="mt-4">
                                <router-link v-if="ticket.status_pembayaran === 'Lunas'"
                                    :to="{ name: 'ETicket', params: { ticketId: ticket.id_tiket } }"
                                    class="bg-green-500 text-white py-2 px-4 rounded text-sm font-semibold hover:bg-green-600">
                                    Lihat E-Ticket
                                </router-link>
                                <button v-else-if="ticket.status_pembayaran === 'Belum Bayar'"
                                    class="bg-orange-500 text-white py-2 px-4 rounded text-sm font-semibold hover:bg-orange-600">
                                    Lanjutkan Pembayaran
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const tickets = ref([]);
const loading = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get('/api/booking/history');
        tickets.value = response.data;
    } catch (error) {
        console.error("Gagal mengambil riwayat tiket:", error);
        // Tambahkan notifikasi error untuk user
    } finally {
        loading.value = false;
    }
});

const getStatusColor = (status) => {
    switch (status) {
        case 'Lunas': return 'bg-green-600';
        case 'Belum Bayar': return 'bg-orange-500';
        case 'Gagal': return 'bg-red-500';
        case 'Kadaluarsa': return 'bg-gray-500';
        default: return 'bg-gray-500';
    }
};
</script>
