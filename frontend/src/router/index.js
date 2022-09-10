import Vue from 'vue'
import VueRouter from 'vue-router'
import AuthRequired from "../utils/AuthRequired";

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'App',
    beforeEnter: AuthRequired,
    component: () => import(/* webpackChunkName: "dashboard-app" */ '../pages'),
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import(/* webpackChunkName: "dashboard" */ '../pages/Dashboard.vue'),
        meta: {
          title: 'Dashboard - Koperasi Baik',
        }
      },
      {
        path: 'saldo-anggota',
        name: 'SaldoAnggota',
        component: () => import(/* webpackChunkName: "SaldoAnggota" */ '../pages/SaldoAnggota.vue'),
        meta: {
          title: 'Saldo Anggota - Koperasi Baik',
        }
      },
      {
        path: 'saldo-tabungan',
        name: 'SaldoTabungan',
        component: () => import(/* webpackChunkName: "SaldoTabungan" */ '../pages/SaldoTabungan.vue'),
        meta: {
          title: 'Saldo Tabungan - Koperasi Baik',
        }
      },
      {
        path: 'saldo-tabungan/history/:product_name/:account_saving_no/:cif_type',
        name: 'StatementTabungan',
        component: () => import(/* webpackChunkName: "SaldoTabungan" */ '../pages/TabunganHistory.vue'),
        meta: {
          title: 'Statement Tabungan - Koperasi Baik',
        }
      },
      {
        path: 'saldo-pembiayaan',
        name: 'SaldoPembiayaan',
        component: () => import(/* webpackChunkName: "SaldoPembiayaan" */ '../pages/SaldoPembiayaan.vue'),
        meta: {
          title: 'Saldo Pembiayaan - Koperasi Baik',
        }
      },
      {
        path: 'saldo-pembiayaan/history/:account_financing_no',
        name: 'StatementPembiayaan',
        component: () => import(/* webpackChunkName: "SaldoPembiayaan" */ '../pages/PembiayaanHistory.vue'),
        meta: {
          title: 'Statement Pembiayaan - Koperasi Baik',
        }
      },
      {
        path: 'detil-pembiayaan',
        name: 'DetailPembiayaan',
        component: () => import(/* webpackChunkName: "DetailPembiayaan" */ '../pages/DetailPembiayaan.vue'),
        meta: {
          title: 'Saldo Pembiayaan - Koperasi Baik',
        }
      },
      {
        path: 'transaksi',
        name: 'Transaksi',
        component: () => import(/* webpackChunkName: "Transaksi" */ '../pages/Transaksi.vue'),
        meta: {
          title: 'Transaksi - Koperasi Baik',
        }
      },
      {
        path: 'transaksi/kirim',
        name: 'TransaksiKirim',
        component: () => import(/* webpackChunkName: "TransaksiKirim" */ '../pages/TransaksiKirim.vue'),
        meta: {
          title: 'Transaksi Kirim - Koperasi Baik',
        }
      },
      {
        path: 'transaksi/terima',
        name: 'TransaksiTerima',
        component: () => import(/* webpackChunkName: "TransaksiTerima" */ '../pages/TransaksiTerima.vue'),
        meta: {
          title: 'Transaksi Terima - Koperasi Baik',
        }
      },
      {
        path: 'transaksi/histori',
        name: 'TransaksiHistori',
        component: () => import(/* webpackChunkName: "TransaksiHistori" */ '../pages/TransaksiHistori.vue'),
        meta: {
          title: 'Transaksi Histori - Koperasi Baik',
        }
      },
      {
        path: 'transaksi/topup',
        name: 'TransaksiTopUp',
        component: () => import(/* webpackChunkName: "TransaksiTopUp" */ '../pages/TransaksiTopUp.vue'),
        meta: {
          title: 'Transaksi TopUp - Koperasi Baik',
        }
      },
      {
        path: 'profile',
        name: 'Profile',
        component: () => import(/* webpackChunkName: "Profile" */ '../pages/Profile.vue'),
        meta: {
          title: 'Profile - Koperasi Baik',
        }
      },
      {
        path: '/pasar-BAIK',
        name: 'PasarBaik',
        component: () => import(/* webpackChunkName: "PasarBaik" */ '../pages/ComingSoon.vue'),
        meta: {
          title: 'Coming Soon - Koperasi Baik',
        }
      },
      {
        path: '/pembelian',
        name: 'Pembelian',
        component: () => import(/* webpackChunkName: "Pembelian" */ '../pages/Pembelian.vue'),
        meta: {
          title: 'Pembelian - Koperasi Baik',
        }
      },
      {
        path: '/pengajuan',
        name: 'Pengajuan',
        component: () => import(/* webpackChunkName: "Pengajuan" */ '../pages/Pengajuan.vue'),
        meta: {
          title: 'Pengajuan - Koperasi Baik',
        }
      },
      {
        path: '/pengajuan/form',
        name: 'PengajuanForm',
        component: () => import(/* webpackChunkName: "PengajuanForm" */ '../pages/PengajuanForm.vue'),
        meta: {
          title: 'Pengajuan - Koperasi Baik',
        }
      },
      {
        path: '/comingsoon',
        name: 'ComingSoon',
        component: () => import(/* webpackChunkName: "ComingSoon" */ '../pages/ComingSoon.vue'),
        meta: {
          title: 'Coming Soon - Koperasi Baik',
        }
      },
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import(/* webpackChunkName: "login" */ '../pages/Login.vue'),
    meta: {
      title: 'Login - Koperasi Baik',
    }
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: () => import(/* webpackChunkName: "forgot-password" */ '../pages/ForgotPassword.vue'),
    meta: {
      title: 'Forgot Password - Koperasi Baik',
    }
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
