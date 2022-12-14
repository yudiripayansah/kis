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
          title: 'Dashboard - KIS',
        }
      },
      {
        path: 'ubah-password',
        name: 'Ubah Password',
        component: () => import(/* webpackChunkName: "ubah-password" */ '../pages/UbahPassword.vue'),
        meta: {
          title: 'Ubah Password - KIS',
        }
      },
      {
        path: 'saldo/:type/:noanggota?',
        name: 'Saldo',
        component: () => import(/* webpackChunkName: "Saldo" */ '../pages/Saldo.vue'),
        meta: {
          title: 'Saldo - KIS',
        }
      },
      {
        path: 'anggota/:rembug?/:anggota?',
        name: 'Anggota',
        component: () => import(/* webpackChunkName: "anggota" */ '../pages/Anggota.vue'),
        meta: {
          title: 'Anggota - KIS',
        }
      },
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import(/* webpackChunkName: "login" */ '../pages/Login.vue'),
    meta: {
      title: 'Login - KIS',
    }
  },
  {
    path: '/forgot-password',
    name: 'Forgot Password',
    component: () => import(/* webpackChunkName: "forgot" */ '../pages/Forgot.vue'),
    meta: {
      title: 'Forgot Password - KIS',
    }
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
