<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Saldo Pembiayaan</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content pb-5">
      <b-overlay :show="saldo.loading">
      <div v-if="saldo.data.length > 0">
        <div class="pg-saldo-pembiayaan mb-3" v-for="(sd,sdIndex) in saldo.data" :key="`saldo-${sdIndex}`">
          <h1>Pembiayaan ke {{sd.pembiayaan_ke}}</h1>
          <div class="font-12 d-flex justify-content-between align-items-center">
            <label>No Rek</label>
            <span>{{sd.account_financing_no}}</span>
          </div>
          <div class="font-12 d-flex justify-content-between align-items-center">
            <label>Pokok</label>
            <span>{{sd.pokok}}</span>
          </div>
          <div class="font-12 d-flex justify-content-between align-items-center">
            <label>Margin</label>
            <span>{{sd.margin}}</span>
          </div>
          <div class="font-12 d-flex justify-content-between align-items-center">
            <label>Jangka Waktu</label>
            <span>{{sd.jangka_waktu}}</span>
          </div>
          <div class="font-12 d-flex justify-content-between align-items-center">
            <label>Status</label>
            <span>{{sd.status_rekening}}</span>
          </div>
          <div class="font-12 d-flex justify-content-between align-items-center">
            <label>Saldo Pokok</label>
            <span>{{sd.saldo_pokok}}</span>
          </div>
          <div class="font-12 d-flex justify-content-between align-items-center">
            <label>Saldo Margin</label>
            <span>{{sd.saldo_margin}}</span>
          </div>
          <div class="font-12 d-flex justify-content-between align-items-center">
            <label>Saldo Catab</label>
            <span>{{sd.saldo_catab}}</span>
          </div>
          <router-link :to="`/saldo-pembiayaan/history/${sd.account_financing_no}`">
            <b-icon icon="credit-card2-back-fill"/> Kartu Angsuran
          </router-link>
        </div>
      </div>
      <div v-else>
        <b-alert variant="warning" :show="!saldo.loading">Anda tidak memiliki saldo pembiayaan</b-alert>
      </div>
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
        data : [],
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
      let url = `${baseUrl}/m_saldo_financing`
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
        if(res.data[0].status != 'TIDAK ADA DATA')
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