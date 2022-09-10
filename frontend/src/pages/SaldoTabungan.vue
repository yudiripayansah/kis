<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Saldo Tabungan</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content pb-5">
      <b-overlay :show="saldo.loading">
      <div v-if="saldo.data.length > 0">
        <router-link :to="`/saldo-tabungan/history/${sd.product_name}/${sd.account_saving_no}/${sd.cif_type}`" v-for="(sd,sdIndex) in saldo.data" :key="`saldo-${sdIndex}`" v-show="sd.product_name">
          <div class="pg-saldo-item green mb-3">
            <h5>{{sd.product_name}}</h5>
            <p>{{sd.account_saving_no}}</p>
            <h1>{{sd.saldo}}</h1>
          </div>
        </router-link>
      </div>
      <div v-else>
        <b-alert variant="warning" :show="!saldo.loading">Anda tidak memiliki saldo Tabungan</b-alert>
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
      let url = `${baseUrl}/m_saldo_saving`
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
        if(res.data[0].status != 'TIDAK ADA DATA')
        this.saldo.data = res.data
        this.saldo.loading = false
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