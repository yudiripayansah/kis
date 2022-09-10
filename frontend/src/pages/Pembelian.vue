<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Pembelian</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content">
      <div class="pg-dashboard-nav">
        <router-link to="/comingsoon">
          <div><i class="fas fa-mobile"></i></div>
          <span>Pulsa</span>
        </router-link>
        <router-link class="pembiayaan" to="/comingsoon">
          <div><i class="fas fa-bolt"></i></div>
          <span>Token Listrik</span>
        </router-link>
        <div class="w-100"></div>
        <router-link class="tabungan" to="/comingsoon">
          <div><i class="fas fa-tint"></i></div>
          <span>PDAM</span>
        </router-link>
        <router-link class="tabungan" to="/comingsoon">
          <div><i class="fas fa-hand-holding-heart"></i></div>
          <span>BPJS</span>
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