<template>
  <div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Pesan Tiket Bus Online</h1>
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <form @submit.prevent="cariJadwal">
        <div class="grid md:grid-cols-4 gap-4">
          <div>
            <label for="asal" class="block text-sm font-medium text-gray-700">Kota Asal</label>
            <input type="text" v-model="form.asal" id="asal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
          </div>
          <div>
            <label for="tujuan" class="block text-sm font-medium text-gray-700">Kota Tujuan</label>
            <input type="text" v-model="form.tujuan" id="tujuan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
          </div>
          <div>
            <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal Berangkat</label>
            <input type="date" v-model="form.tanggal_berangkat" id="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
          </div>
          <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">Cari Tiket</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const form = reactive({
  asal: '',
  tujuan: '',
  tanggal_berangkat: new Date().toISOString().split('T')[0], // Default hari ini
});

const cariJadwal = () => {
  // Arahkan ke halaman hasil pencarian dengan query
  router.push({
    name: 'SearchResult',
    query: {
      asal: form.asal,
      tujuan: form.tujuan,
      tanggal: form.tanggal_berangkat
    }
  });
};
</script>
