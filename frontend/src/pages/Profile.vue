<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Profile</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content">
      <div>
        <b-form-group label="Nama">
          <b-input v-model="profile.name" disabled/>
        </b-form-group>
        <b-form-group label="Cabang">
          <b-input v-model="profile.branch_name" disabled/>
        </b-form-group>
        <b-form-group label="Majelis">
          <b-input v-model="profile.cm_name" disabled/>
        </b-form-group>
        <b-form-group label="No Anggota">
          <b-input v-model="profile.cif_no" disabled/>
        </b-form-group>
        <!-- <b-form-group label="Password">
          <b-input-group>
            <b-input v-model="user.password" :type="(showpass) ? 'text' : 'password'"/>
            <b-input-group-append>
              <b-button @click="showpass = !showpass"><i class="fa fa-eye"></i></b-button>
            </b-input-group-append>
          </b-input-group>
        </b-form-group> -->
        <b-form-group label="Ubah Password" description="Isi bila ingin mengubah password">
          <b-input-group>
            <b-input v-model="profile.password" :type="(showpass) ? 'text' : 'password'"/>
            <b-input-group-append>
              <b-button @click="showpass = !showpass"><i class="fa fa-eye"></i></b-button>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
        <b-button type="submit" variant="primary" block @click="doSave()">Simpan</b-button>
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
      app :settings,
      profile : {
        branch_name:  null,
        cm_name: null,
        cif_no: null,
        name:  null,
        saldo: null,
        message: null,
        password : null
      },
      showpass : false
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
        this.profile.password = null
      })
      .catch((res)=>{
        this.profile.loading = false
        this.notif('Error',res.message,'danger')
      })
    },
    doSave(){
      if(this.profile.password){
        this.profile.loading = true
        let url = `${baseUrl}/m_update_password`
        let payloadData = {
          password : this.profile.password,
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
          if(res.data.status == 1){
            this.notif('Success',res.data.status_message,'success')
            this.getProfile()
          } else {
            this.notif('Error',res.data.status_message,'danger')
          }
          this.profile.password = null
        })
        .catch((res)=>{
          this.profile.loading = false
          this.notif('Error',res.message,'danger')
        })
      } else {
        this.notif('Warning','Masukan password baru bila ingin mengubah password','warning')
      }
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