<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Dashboard</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content">
      <div class="pg-profile-box d-flex align-items-center justify-content-between">
        <div class="d-flex">
          <img src="../assets/images/profile.png" alt="">
          <div class="pg-profile-box-text">
            <h3>Hi, {{profile.name}}</h3>
            <h6>Dompet {{app.koperasi_name}}</h6>
            <h2>
              {{profile.saldo}}
            </h2>
          </div>
        </div>
        <div @click="doLogout()" class="d-flex justify-content-center align-items-center pg-btn-logout">
          <i class="fas fa-sign-out-alt"></i>
        </div>
      </div>
      <div class="pg-dashboard-nav">
        <router-link to="/saldo-anggota">
          <div><b-icon icon="person"/></div>
          <span>Saldo Anggota</span>
        </router-link>
        <router-link class="pembiayaan" to="/saldo-pembiayaan">
          <div><b-icon icon="credit-card2-back-fill"/></div>
          <span>Saldo Pembiayaan</span>
        </router-link>
        <router-link class="tabungan" to="/saldo-tabungan">
          <div><i class="fas fa-money-bill-wave"></i></div>
          <span>Saldo Tabungan</span>
        </router-link>
        <div class="w-100"></div>
        <router-link class="transaksi" to="/transaksi">
          <div><b-icon icon="hand-index"/></div>
          <span>Transaksi</span>
        </router-link>
        <router-link class="tabungan" to="/pembelian">
          <div><i class="fas fa-shopping-basket"></i></div>
          <span>Pembelian</span>
        </router-link>
        <a href="https://pasarbaik.bmbaytulikhtiar.com/" target="_blank">
          <div>
            <i class="fas fa-store-alt"></i>
          </div>
          <span>Pasar {{app.koperasi_name}}</span>
        </a>
        <div class="w-100"></div>
        <router-link class="pembiayaan" to="/pengajuan" v-show="user.cif_type == 1">
          <div><i class="fas fa-clipboard-check"></i></div>
          <span>Pengajuan</span>
        </router-link>
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
        branch_name:  null,
        cm_name: null,
        cif_no: null,
        name:  null,
        saldo: null,
        message: null
      }
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
      let url = `${baseUrl}/m_view_profile`
      let payloadData = {
        cif_no : this.user.cif_no,
        token : this.user.token,
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      axios
      .post(url,payload)
      .then((res)=>{
        this.profile.loading = false
        this.profile = res.data
      })
      .catch((res)=>{
        this.profile.loading = false
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
  }
}
</script>