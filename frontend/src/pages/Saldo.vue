<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6 class="text-capitalize">Saldo {{ $route.params.type }}</h6>
      <div class="pg-header-nav-btn">
        <router-link :to="$route.params.noanggota ? `/anggota` :`/dashboard`">
          <i class="fas fa-arrow-left"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content">
      <div class="pg-profile-box d-flex align-items-center justify-content-between">
        <div class="d-flex">
          <img src="../assets/images/profile.png" alt="">
          <div class="pg-profile-box-text">
            <h2>{{profile.nama}}</h2>
            <h3>{{profile.noanggota}}</h3>
            <h3>{{profile.majelis}} <small>({{profile.desa}})</small></h3>
          </div>
        </div>
      </div>
      <div class="pg-saldo py-3 d-flex flex-column align-items-center">
        <h4 class="mb-4">Histori</h4>
        <div class="pg-saldo-items w-100" v-for="(h,hIndex) in histori" :key="hIndex">
          <div class="mb-3 rounded p-3 bg-green-3 text-lg-1 border-3 border border-green-1" v-if="$route.params.type != 'tabungan-berjangka' && $route.params.type != 'pembiayaan'">
            <h6><small>{{h.trx_date}}</small></h6>
            <div class="d-flex justify-content-between align-items-end">
              <label>Saldo Awal</label>
              <span>Rp {{h.saldo_awal}}</span>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>Setor</label>
              <span>Rp {{h.setor}}</span>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>Tarik</label>
              <span>Rp {{h.tarik}}</span>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>Saldo Akhir</label>
              <span>Rp {{h.saldo}}</span>
            </div>
          </div>
          <div class="mb-3 rounded p-3 bg-green-3 text-lg-1 border-3 border border-green-1" v-if="$route.params.type == 'tabungan-berjangka'">
            <h6><small>{{h.trx_date}}</small></h6>
            <div class="d-flex justify-content-between align-items-end">
              <label>Saldo Awal</label>
              <span>Rp {{h.saldo_awal}}</span>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>Setor</label>
              <span>Rp {{h.amount_trx}}</span>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>Saldo Akhir</label>
              <span>Rp {{h.saldo}}</span>
            </div>
          </div>
          <div class="mb-3 rounded p-3 bg-green-3 text-lg-1 border-3 border border-green-1" v-if="$route.params.type == 'pembiayaan'">
            <h6>Angsuran Ke {{h.angs_ke}}</h6>
            <div class="d-flex justify-content-between align-items-end">
              <label>Tgl Jatuh Tempo</label>
              <span>{{h.tgl_jtempo}}</span>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>Tanggal Bayar</label>
              <span>{{h.tgl_bayar}}</span>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>Angsuran Pokok</label>
              <span>Rp {{h.angs_pokok}}</span>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>Saldo Margin</label>
              <span>Rp {{h.saldo_margin}}</span>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>Saldo Pokok</label>
              <span>Rp {{h.saldo_pokok}}</span>
            </div>
          </div>
        </div>
        <div class="pg-saldo-items w-100" v-if="histori.length < 1">
          <div class="mb-3 rounded p-3 bg-yellow-1 text-dg-3 border-3 border border-yellow-1 text-center p-3">
            Belum ada transaksi
          </div>
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
      profile : Object,
      histori : []
    }
  },
  computed: {
    ...mapGetters(["user"])
  },
  watch: {
    user(val){
      let user = val
      if(user && user.token && user.cif_no){
        this.$router.push("/");
      } else {
        this.$router.push("/login");
      }
    }
  },
  methods: {
    ...mapActions(["logout"]),
    getProfile(){
      this.profile.loading = true
      let url = `${baseUrl}information/dashboard`
      let noanggota = this.$route.params.noanggota
      let payloadData = {
        id_user : this.user.id_user,
        tipe_user : this.user.tipe_user,
      }
      if(noanggota) {
        payloadData = {
          id_user : noanggota,
          tipe_user : 1,
        }
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      let config = {
        headers: {
          'Token': this.user.token
        }
      }
      axios
      .post(url,payload,config)
      .then((res)=>{
        this.profile.loading = false
        const { data } = res.data
        this.profile = data
      })
      .catch((res)=>{
        this.profile.loading = false
        this.notif('Error',res.message,'danger')
      })
    },
    getHistori(){
      let url = `${baseUrl}information/history_member_saving`
      let type = this.$route.params.type
      let noanggota = this.$route.params.noanggota
      let payloadData = {
        id_user : this.user.id_user
      }
      if(noanggota){
        payloadData = {
          noanggota : noanggota
        }
      }
      if(type == 'simpok') {
        payloadData.jenis_trx = 1
      }
      if(type == 'simwa') {
        payloadData.jenis_trx = 2
        // payloadData.jenis_trx = 3
      }
      if(type == 'sukarela') {
        payloadData.jenis_trx = 4
      }
      if(type == 'tabungan-berjangka'){
        url = `${baseUrl}information/history_member_deposito`
      }
      if(type == 'pembiayaan'){
        url = `${baseUrl}information/history_member_financing`
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      let config = {
        headers: {
          'Token': this.user.token
        }
      }
      axios
      .post(url,payload,config)
      .then((res)=>{
        const { data } = res.data
        this.histori = data
      })
      .catch((res)=>{
        this.notif('Error',res.message,'danger')
      })
    },
    doLogout(){
      this.logout()
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
    this.getProfile()
    this.getHistori()
  }
}
</script>