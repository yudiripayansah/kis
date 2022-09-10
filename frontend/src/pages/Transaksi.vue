<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Transaksi</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content">
      <div class="pg-dashboard-nav">
        <router-link to="/transaksi/kirim">
          <div><i class="fas fa-sign-out-alt"></i></div>
          <span>Kirim</span>
        </router-link>
        <router-link class="pembiayaan" to="/transaksi/terima">
          <div><i class="fas fa-sign-in-alt"></i></div>
          <span>Terima</span>
        </router-link>
        <div class="w-100"></div>
        <router-link class="tabungan" to="/transaksi/histori">
          <div><i class="fas fa-history"></i></div>
          <span>Histori</span>
        </router-link>
        <router-link class="transaksi" to="/transaksi/topup">
          <div><i class="fas fa-arrow-up"></i></div>
          <span>Top Up</span>
        </router-link>
      </div>
    </div>
  </div>
</template>
<script>
import {baseUrl,settings} from '../config'
export default {
  data(){
    return {
      app :settings
    }
  }
}
</script>