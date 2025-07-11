import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

import AppLayout from '../layouts/AppLayout.vue';
import OperatorLayout from '../layouts/OperatorLayout.vue';

import Home from '../views/Home.vue';
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import SearchResult from '../views/SearchResult.vue';
import BookingDetail from '../views/BookingDetail.vue';
import NotFound from '../views/NotFound.vue';

import Profile from '../views/penumpang/Profile.vue';
import BookingHistory from '../views/penumpang/BookingHistory.vue';
import ETicket from '../views/penumpang/ETicket.vue';
import Payment from '../views/penumpang/Payment.vue';

import OperatorDashboard from '../views/operator/Dashboard.vue';
import ManageJadwal from '../views/operator/ManageJadwal.vue';
import PassengerList from '../views/operator/PassengerList.vue';
import VerifyTicket from '../views/operator/VerifyTicket.vue';

const routes = [
    {
        path: '/',
        component: AppLayout,
        children: [
            { path: '', name: 'Home', component: Home },
            { path: 'login', name: 'Login', component: Login },
            { path: 'register', name: 'Register', component: Register },
            { path: 'search', name: 'SearchResult', component: SearchResult, props: route => ({ query: route.query }) },
            { path: 'jadwal/:id', name: 'BookingDetail', component: BookingDetail, props: true },

            // == RUTE PENUMPANG (Wajib Login, Role: Penumpang) ==
            {
                path: 'profile',
                name: 'Profile',
                component: Profile,
                meta: { requiresAuth: true, roles: ['penumpang'] }
            },
            {
                path: 'my-tickets',
                name: 'BookingHistory',
                component: BookingHistory,
                meta: { requiresAuth: true, roles: ['penumpang'] }
            },
            {
                path: 'payment/:bookingId',
                name: 'Payment',
                component: Payment,
                props: true,
                meta: { requiresAuth: true, roles: ['penumpang'] }
            },
            {
                path: 'e-ticket/:ticketId',
                name: 'ETicket',
                component: ETicket,
                props: true,
                meta: { requiresAuth: true, roles: ['penumpang'] }
            },
        ]
    },
    {
        // == RUTE OPERATOR (Wajib Login, Role: Operator atau Admin) ==
        path: '/operator',
        component: OperatorLayout, // Layout khusus untuk dashboard operator
        meta: { requiresAuth: true, roles: ['operator', 'admin'] },
        children: [
            { path: 'dashboard', name: 'OperatorDashboard', component: OperatorDashboard },
            { path: 'jadwal', name: 'ManageJadwal', component: ManageJadwal },
            { path: 'jadwal/:id/penumpang', name: 'PassengerList', component: PassengerList, props: true },
            { path: 'verifikasi', name: 'VerifyTicket', component: VerifyTicket },
        ]
    },
    // Rute Admin bisa ditambahkan dengan cara yang sama
    // {
    //     path: '/admin',
    //     component: AdminLayout,
    //     meta: { requiresAuth: true, roles: ['admin'] },
    //     children: [ ... ]
    // }

    { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;
    const userRole = authStore.user?.role;

    if (!authStore.user && authStore.token) {
        await authStore.fetchUser();
    }

    const requiredRoles = to.meta.roles;

    // 1. Jika rute memerlukan login
    if (to.meta.requiresAuth) {
        // 1a. Jika user tidak terautentikasi, redirect ke halaman login
        if (!isAuthenticated) {
            next({
                name: 'Login',
                // Simpan lokasi yang ingin dituju agar bisa redirect kembali setelah login
                query: { redirect: to.fullPath }
            });
        }
        // 1b. Jika user terautentikasi tetapi rolenya tidak diizinkan
        else if (requiredRoles && !requiredRoles.includes(userRole)) {
            alert("Anda tidak memiliki hak akses ke halaman ini.");
            next({ name: 'Home' }); // Redirect ke halaman utama atau halaman 'unauthorized'
        }
        // 1c. Jika user terautentikasi dan memiliki role yang sesuai
        else {
            next();
        }
    }
    // 2. Jika rute tidak memerlukan login
    else {
        next();
    }
});

export default router;
