<template>
  <div class="container mx-auto p-8">
    <h2 class="text-2xl font-bold mb-4">Hasil Pencarian Jadwal</h2>
    <div v-if="loading" class="text-center">Loading...</div>
    <div v-if="error" class="text-center text-red-500">{{ error }}</div>

    <div v-if="jadwals.length > 0" class="space-y-4">
      <div v-for="jadwal in jadwals" :key="jadwal.id_jadwal" class="bg-white p-4 rounded-lg shadow flex justify-between items-center">
        <div>
          <p class="font-bold text-lg">{{ jadwal.bus.nama_bus }} ({{ jadwal.bus.kelas }})</p>
          <p>{{ jadwal.asal }} -> {{ jadwal.tujuan }}</p>
          <p>{{ jadwal.jam_berangkat }}</p>
        </div>
        <div>
          <p class="font-semibold text-xl text-blue-600">Rp {{ formatHarga(jadwal.harga) }}</p>
          <p class="text-sm text-gray-600">{{ jadwal.kursi_tersedia }} kursi tersedia</p>
          <button @click="pilihJadwal(jadwal.id_jadwal)" class="mt-2 bg-green-500 text-white py-1 px-3 rounded">Pilih</button>
        </div>
      </div>
    </div>
    <div v-else-if="!loading && !error" class="text-center text-gray-500">
      Jadwal tidak ditemukan.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios'; // Sebaiknya gunakan instance axios terkonfigurasi

const route = useRoute();
const router = useRouter();
const jadwals = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchJadwals = async () => {
  try {
    const params = {
      asal: route.query.asal,
      tujuan: route.query.tujuan,
      tanggal_berangkat: route.query.tanggal
    };
    const response = await axios.get('/api/jadwal/search', { params });
    jadwals.value = response.data;
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal memuat jadwal.';
  } finally {
    loading.value = false;
  }
};

const formatHarga = (value) => {
    return new Intl.NumberFormat('id-ID').format(value);
};

const pilihJadwal = (id) => {
    router.push({ name: 'BookingDetail', params: { id } });
};

onMounted(fetchJadwals);
</script>
