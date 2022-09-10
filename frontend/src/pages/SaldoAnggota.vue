<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Saldo Anggota</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content pb-5">
      <b-overlay :show="saldo.loading">
        <div class="pg-saldo-item blue mb-3">
          <h5>Saldo Simpanan Pokok</h5>
          <!-- <p>desc</p> -->
          <h1>{{saldo.data.simpok}}</h1>
        </div>
        <div class="pg-saldo-item blue mb-3">
          <h5>Saldo Simpanan Wajib</h5>
          <!-- <p>desc</p> -->
          <h1>{{saldo.data.simwa}}</h1>
        </div>
        <router-link :to="`/saldo-tabungan/history/${saldo.data.product_name}/${saldo.data.account_saving_no}/${saldo.data.cif_type}`">
          <div class="pg-saldo-item blue mb-5">
            <h5>Saldo Sukarela</h5>
            <!-- <p>desc</p> -->
            <h1>{{saldo.data.sukarela}}</h1>
          </div>
        </router-link>
        </b-overlay>
    </div>
  </div>
</template>
<script>
import {
  mapGetters,
  mapActions
} from "vuex";
import axios from 'axios'
import {baseUrl,settings} from '../config'
export default {
  data(){
    return {
      app :settings,
      saldo : {
        data : {
          simwa: 0,
          sukarela: 0,
          simpok: 0,
          status : null,
          message : null
        },
        loading : false
      }
    }
  },
  computed: {
    ...mapGetters(["user"])
  },
  methods: {
    getSaldo(){
      this.saldo.loading = true
      let url = `${baseUrl}/m_saldo_membership`
      let payloadData = {
        cif_no : this.user.cif_no,
        token : this.user.token
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      axios
      .post(url,payload)
      .then((res)=>{
        this.saldo.loading = false
        this.saldo.data = res.data
      })
      .catch((res)=>{
        this.saldo.loading = false
        this.notif('Error',res.message,'danger')
      })
    },
    thousand(num) {
      if (num) {
        let num_parts = num.toString().split(",");
        num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return num_parts.join(",");
      } else {
        return 0
      }
    },
    notif(title,msg,type){
      this.$bvToast.toast(msg, {
        title: title,
        autoHideDelay: 5000,
        variant: type,
        toaster: 'b-toaster-bottom-center'
      })
    }
  },
  mounted(){
    this.getSaldo()
  }
}
</script>