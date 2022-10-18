<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Anggota</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/dashboard">
          <i class="fas fa-arrow-left"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content">
      <div class="pg-profile-box d-flex align-items-center justify-content-between w-100 color-2 flex-column" v-for="(a,aIndex) in anggota" :key="aIndex">
        <div class="d-flex w-100">
          <img src="/assets/images/profile.png" alt="">
          <div class="pg-profile-box-text">
            <h2>{{a.nama}}</h2>
            <h3>{{a.noanggota}}</h3>
            <h3>{{a.majelis}} <small>({{a.desa}})</small></h3>
          </div>
        </div>
        <div class="pg-dashboard-nav small">
          <router-link :to="`/saldo/simpok/${a.noanggota}`">
            <div>
              <span>Saldo Simpok</span>
              Rp {{a.saldo.simpok}}
            </div>
          </router-link>
          <router-link :to="`/saldo/simwa/${a.noanggota}`" class="color-1">
            <div>
              <span>Saldo Simwa</span>
              Rp {{a.saldo.simwa}}
            </div>
          </router-link>
          <router-link :to="`/saldo/sukarela/${a.noanggota}`" class="color-2">
            <div>
              <span>Saldo Sukarela</span>
              Rp {{a.saldo.sukarela}}
            </div>
          </router-link>
          <router-link :to="`/saldo/tabungan-berjangka/${a.noanggota}`" class="color-3">
            <div>
              <span>Saldo Tabungan Berjangka</span>
              Rp {{a.saldo.saldo_deposito}}
            </div>
          </router-link>
          <router-link :to="`/saldo/pembiayaan/${a.noanggota}`" class="color-4">
            <div>
              <span>Saldo Pembiayaan</span>
              Rp {{a.saldo.saldo_outstanding}}
            </div>
          </router-link>
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
      anggota: [
        {
          saldo: Object  
        }
      ]
    }
  },
  computed: {
    ...mapGetters(["user"])
  },
  methods: {
    ...mapActions(["logout"]),
    getProfile(){
      this.profile.loading = true
      let url = `${baseUrl}information/dashboard`
      let payloadData = {
        id_user : this.user.id_user,
        tipe_user : this.user.tipe_user,
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
    async getAnggota(){
      let url = `${baseUrl}information/member`
      let config = {
        headers: {
          'Token': this.user.token
        }
      }
      let resAnggota = await axios.get(url,config)
      let {data} = resAnggota.data
      data.map(async (item) => {
        item.saldo = Object
        let payload = new FormData()
        payload.append('noanggota',item.noanggota)
        let resSaldo = await axios.post(`${baseUrl}information/saldo_member`,payload,config)
        item.saldo = resSaldo.data.data
      })
      this.anggota = data
    },
    doLogout(){
      this.logout()
      this.$router.push('/login').catch(()=>{})
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
    this.getAnggota()
  }
}
</script>