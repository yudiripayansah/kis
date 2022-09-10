<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Kartu Angsuran</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content pb-5">
      <div class="pg-saldo-pembiayaan mb-3" v-for="(sd,sdIndex) in saldo.data" :key="`saldo-${sdIndex}`">
        <h1>{{sd.title}}</h1>
        <div class="d-flex justify-content-between align-items-center">
          <label>No Rekening</label>
          <span>{{sd.no_rek}}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <label>Pokok</label>
          <span>{{sd.pokok}}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <label>Margin</label>
          <span>{{sd.margin}}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <label>Simsus</label>
          <span>{{sd.simsus}}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <label>Angsuran</label>
          <span>{{sd.angsuran}}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <label>Tanggal Bayar</label>
          <span>{{sd.tanggal}}</span>
        </div>
      </div>
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
      app : settings,
      saldo : {
        data : [
          {
            title : 'Angsuran ke 1',
            no_rek : '1234567890',
            pokok : 'Rp. 10.000.000',
            margin : 'Rp. 200.000',
            simsus : 'Rp. 100',
            angsuran : 'Rp. 20.000',
            tanggal : '2 Maret 2021'
          },
          {
            title : 'Angsuran ke 1',
            no_rek : '1234567890',
            pokok : 'Rp. 10.000.000',
            margin : 'Rp. 200.000',
            simsus : 'Rp. 100',
            angsuran : 'Rp. 20.000',
            tanggal : '2 Maret 2021'
          },
          {
            title : 'Angsuran ke 1',
            no_rek : '1234567890',
            pokok : 'Rp. 10.000.000',
            margin : 'Rp. 200.000',
            simsus : 'Rp. 100',
            angsuran : 'Rp. 20.000',
            tanggal : '2 Maret 2021'
          }
        ]
      }
    }
  },
  computed: {
    ...mapGetters(["user"])
  },
  methods: {
    getSaldo(){
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
        this.saldo.data = res.data
      })
      .catch((res)=>{
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