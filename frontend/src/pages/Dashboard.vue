<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Dashboard</h6>
      <!-- <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div> -->
    </div>
    <div class="pg-content">
      <div class="pg-profile-box d-flex align-items-center justify-content-between">
        <div class="d-flex">
          <img src="/assets/images/profile.png" alt="">
          <div class="pg-profile-box-text" v-if="profile.nama">
            <h2>Hi, {{profile.nama}}</h2>
            <h3>{{profile.noanggota}}</h3>
            <h3>{{profile.majelis}} <small>({{profile.desa}})</small></h3>
            <router-link to="/ubah-password">Ubah Password</router-link>
          </div>
          <div class="pg-profile-box-text" v-else>
            <h2>Hi, Pengelola</h2>
            <h3>Koperasi Syariah KIS</h3>
          </div>
        </div>
        <div @click="doLogout()" class="d-flex justify-content-center align-items-center pg-btn-logout">
          <i class="fas fa-sign-out-alt"></i>
        </div>
      </div>
      <div class="pg-dashboard-nav">
        <b-row>
          <b-col cols="12">
            <router-link to="#" class="color-3" v-if="profile.jumlah">
              <div class="py-2">
                <span>Jumlah Anggota</span>
                {{profile.jumlah}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="12">
            <router-link :to="Number(user.tipe_user) == 2 ? `#` : `/saldo/simpok`">
              <div class="py-2">
                <span>Simpok</span>
                Rp {{profile.simpok}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="6">
            <router-link :to="Number(user.tipe_user) == 2 ? `#` : `/saldo/simwam`" class="color-1">
              <div class="py-2">
                <span>Simwa Mingguan</span>
                Rp {{profile.simwa}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="6">
            <router-link :to="Number(user.tipe_user) == 2 ? `#` : `/saldo/umroh`" class="color-4">
              <div class="py-2">
                <span>Umroh</span>
                Rp {{profile.umroh}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="6">
            <router-link :to="Number(user.tipe_user) == 2 ? `#` : `/saldo/simwa`" class="color-3">
              <div class="py-2">
                <span>Simwa 4%</span>
                Rp {{profile.simwa4}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="6">
            <router-link :to="Number(user.tipe_user) == 2 ? `#` : `/saldo/qurban`" class="color-2">
              <div class="py-2">
                <span>Qurban</span>
                Rp {{profile.qurban}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="6">
            <router-link :to="Number(user.tipe_user) == 2 ? `#` : `/saldo/sukarela`" class="color-2">
              <div class="py-2">
                <span>Sukarela</span>
                Rp {{profile.sukarela}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="6">
            <router-link :to="Number(user.tipe_user) == 2 ? `#` : `/saldo/pendidikan`">
              <div class="py-2">
                <span>Pendidikan</span>
                Rp {{profile.pendidikan}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="6">
            <router-link :to="Number(user.tipe_user) == 2 ? `#` : `/saldo/pembiayaan`" class="color-4">
              <div class="py-2">
                <span>Pembiayaan</span>
                Rp {{profile.saldo_outstanding}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="6">
            <router-link :to="Number(user.tipe_user) == 2 ? `#` : `/saldo/tabungan-berjangka`" class="color-3">
              <div class="py-2">
                <span>Berjangka</span>
                Rp {{profile.saldo_deposito}}
              </div>
            </router-link>
          </b-col>
          <b-col cols="12">
            <router-link to="/anggota" v-if="profile.jumlah">
              <div class="py-3">
                <h6 class="m-0"><b>Lihat Anggota</b></h6>
              </div>
            </router-link>
          </b-col>
        </b-row>
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
      profile : {
        nama: null,
        noanggota: null,
        majelis: null,
        desa: null,
      }
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
  }
}
</script>