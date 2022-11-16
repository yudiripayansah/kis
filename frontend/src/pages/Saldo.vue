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
        <b-row class="w-100 mb-4" no-gutters>
          <b-col cols="6" class="pr-2" v-if="$route.params.type != 'pembiayaan'">
            <b-form-datepicker v-model="from_date" @input="getHistori()"/>
          </b-col>
          <b-col cols="6" class="pl-2" v-if="$route.params.type != 'pembiayaan'">
            <b-form-datepicker v-model="thru_date" @input="getHistori()"/>
          </b-col>
          <b-col cols="12" v-if="$route.params.type == 'pembiayaan'">
            <b-form-select v-model="selectedRekening" :options="rekening" @change="getHistori()"/>
          </b-col>
        </b-row>
        <b-row v-if="activeRekening.tgl_droping" class="mb-3">
          <b-col cols="6">Tanggal Cair</b-col>
          <b-col cols="6" class="text-right">{{ activeRekening.tgl_droping }}</b-col>
          <b-col cols="6">Jangka Waktu</b-col>
          <b-col cols="6" class="text-right">{{ activeRekening.jk_waktu }}</b-col>
          <b-col cols="6">Pokok</b-col>
          <b-col cols="6" class="text-right">Rp {{ activeRekening.pokok }}</b-col>
          <b-col cols="6">Margin</b-col>
          <b-col cols="6" class="text-right">Rp {{ activeRekening.margin }}</b-col>
          <b-col cols="6">Status</b-col>
          <b-col cols="6" class="text-right">{{ activeRekening.status }}</b-col>
        </b-row>
        <div class="pg-saldo-items w-100" v-for="(h,hIndex) in histori" :key="hIndex">
          <div class="mb-3 rounded p-3 border-3 border" v-if="$route.params.type != 'tabungan-berjangka' && $route.params.type != 'pembiayaan'" :class="(h.tarik != '0') ? 'text-red-3 border-red-3' : 'text-green-3 border-green-3'">
            <div class="d-flex justify-content-between align-items-end">
              <label>{{h.trx_date}}</label>
            </div>
            <div class="d-flex justify-content-between align-items-end">
              <label>{{h.keterangan}}</label>
            </div>
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
          <div class="mb-3 rounded p-3 text-green-3 border-green-3 border-3 border" v-if="$route.params.type == 'tabungan-berjangka'">
            <div class="d-flex justify-content-between align-items-end">
              <label>{{h.trx_date}}</label>
            </div>
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
          <div class="mb-3 rounded p-3 border" v-if="$route.params.type == 'pembiayaan'" :class="(h.tgl_bayar == 'Belum Dibayar') ? 'text-red-3 border-red-3' : 'text-green-3 border-green-3'">
            <div class="d-flex justify-content-between align-items-end">
              <label>Angsuran Ke {{h.angs_ke}}</label>
            </div>
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
              <label>Angsuran Margin</label>
              <span>Rp {{h.angs_margin}}</span>
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
      histori : [],
      rekening: [],
      selectedRekening: null,
      activeRekening: {
        tgl_droping: null,
        pokok: null,
        margin: null,
        jk_waktu: null
      },
      from_date: new Date(),
      thru_date: new Date(),
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
    getRekening(){
      let url = `${baseUrl}information/financing`
      let payloadData = {
        id_user : this.user.id_user,
      }
      if(this.user.tipe_user == 2){
        payloadData.id_user = this.$route.params.noanggota
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      let config = {
        headers: {
          'token': this.user.token
        }
      }
      this.rekening = []
      axios
      .post(url,payload,config)
      .then((res)=>{
        const { data } = res.data
        data.map((item) => {
          this.rekening.push({
            value: item.nomrek,
            text: item.produk + ' - ' + item.nomrek,
            data: item
          })
        })
      })
      .catch((res)=>{
        this.notif('Error',res.message,'danger')
      })
    },
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
          'token': this.user.token
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
        id_user : this.user.id_user,
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
      }
      if(type == 'simwam') {
        payloadData.jenis_trx = 3
      }
      if(type == 'sukarela') {
        payloadData.jenis_trx = 4
      }
      if(type == 'umroh') {
        payloadData.jenis_trx = 5
      }
      if(type == 'qurban') {
        payloadData.jenis_trx = 6
      }
      if(type == 'pendidikan') {
        payloadData.jenis_trx = 7
      }
      if(type == 'tabungan-berjangka'){
        url = `${baseUrl}information/history_member_deposito`
      }
      if(type == 'pembiayaan'){
        url = `${baseUrl}information/history_member_financing`
      }
      let fd = new Date(this.from_date)
      let td = new Date(this.thru_date)
      payloadData.from_date = `${fd.getFullYear()}-${fd.getMonth()+1}-${fd.getDate()}`
      payloadData.thru_date = `${td.getFullYear()}-${td.getMonth()+1}-${td.getDate()}`
      this.activeRekening = {
        tgl_droping: null,
        pokok: null,
        margin: null,
        jk_waktu: null
      }
      if(type == 'pembiayaan'){
        payloadData = {
          nomrek: this.selectedRekening
        }
        let activeRek = this.rekening.find((item) => {
          return item.value == this.selectedRekening
        })
        this.activeRekening = activeRek.data
        console.log(activeRek.data)
        console.log(this.activeRekening)
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      let config = {
        headers: {
          'token': this.user.token
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
    // this.getHistori()
    this.getRekening()
    var date = new Date();
    date.setDate(date.getDate() - 30);
    var dateString = date.toISOString().split('T')[0];
    this.from_date = dateString
  }
}
</script>