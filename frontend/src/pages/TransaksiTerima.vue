
<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Transaksi Terima</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content">
      <div class="d-flex flex-column align-items-center justify-content-center">
      <!-- <b-img :src="require('../assets/images/qrcode.png')" alt="" fluid/> -->
      <vue-qrcode :value="user.cif_no" :options="{ width: 300 }"></vue-qrcode>
      <span>No Rek : {{user.cif_no}}</span>
      <span>A.n {{user.nama}}</span>
      </div>
    </div>
  </div>
</template>
<script>
import {
  mapGetters,
  mapActions
} from "vuex";
import VueQrcode from '@chenfengyuan/vue-qrcode';
import {baseUrl,settings} from '../config'
export default {
  components: {
    VueQrcode
  },
  computed: {
    ...mapGetters(["user"])
  },
  data(){
    return {
      app :settings
    }
  }
}
</script>