<template>
    <div class="pg-dashboard pg-page">
      <div class="pg-header pt-5 pb-3">
        <img :src="app.koperasi_logo" alt="">
        <h6>Ubah Password</h6>
        <div class="pg-header-nav-btn">
            <router-link to="/dashboard">
            <i class="fas fa-arrow-left"></i>
            </router-link>
        </div>
      </div>
      <div class="pg-content">
        <div class="pg-profile-box d-flex align-items-center justify-content-between">
          <div class="d-flex">
            <img src="/assets/images/profile.png" alt="">
            <div class="pg-profile-box-text" v-if="profile.nama">
              <h2>Hi, {{profile.nama}}</h2>
              <h3>{{profile.noanggota}}</h3>
              <h3>{{profile.majelis}} <small>({{profile.desa}})</small></h3>
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
            <form @submit.prevent="ubahPassword" class="d-flex flex-column align-items-center justify-content-center w-100">
                <b-form-group class="w-100 mt-1">
                    <b-input-group>
                        <b-input class="fm-input" placeholder="Masukan Password Baru" v-model="form.data.password" :type="(showpass) ? 'text' : 'password'"/>
                        <b-input-group-append>
                            <b-button @click="showpass = !showpass"><i class="fa fa-eye"></i></b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
                <b-form-group class="w-100 mt-1">
                <b-input-group>
                    <b-input class="fm-input" placeholder="Konfirmasi Password Baru" :type="(showpass) ? 'text' : 'password'" v-model="form.data.confirm_password"/>
                    <b-input-group-append>
                        <b-button @click="showpass = !showpass"><i class="fa fa-eye"></i></b-button>
                    </b-input-group-append>
                </b-input-group>
                </b-form-group>
                <b-button type="submit" variant="default" class="fm-btn mt-3 text-light" :disabled="form.loading">
                <b-spinner small variant="light" label="Spinning" v-show="form.loading" class="mr-2"/> UBAH PASSWORD
                </b-button>
            </form>
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
        },
        showpass: false,
        form: {
            data: {
                password: null,
                confirm_password: null,
            }
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
      async ubahPassword(){
        let config = {
          headers: {
            'token': this.user.token
          }
        }
        let url = `${baseUrl}auth/change_password`
        let payload = new FormData()
        payload.append('username', this.profile.noanggota)
        payload.append('password', this.form.data.password)
        payload.append('confirm_password', this.form.data.confirm_password)
        try {
            let res = await axios.post(url,payload,config)
            let {data,status} = res
            if(status === 200){
                let {msg} = data
                this.notif('Success',msg,'success')
                this.form.data = {
                    password: null,
                    confirm_password: null,
                }
            } else {
                this.notif('Error',data.message,'danger')
            }
        } catch (error) {
            this.notif('Error',error.message,'danger')
        }
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