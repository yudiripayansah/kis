<template>
  <div class="pg-login pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <h1>Login</h1>
    </div>
    <div class="pg-content">
      <form @submit.prevent="doLogin" class="d-flex flex-column align-items-center justify-content-center w-100">
        <b-form-group class="w-100">
          <b-input class="fm-input" placeholder="Username / Userid" v-model="form.data.id_user"/>
        </b-form-group>
        <b-form-group class="w-100 mt-1" v-if="loginState">
          <b-input-group>
            <b-input class="fm-input" placeholder="Password" v-model="form.data.password" :type="(showpass) ? 'text' : 'password'"/>
            <b-input-group-append>
              <b-button @click="showpass = !showpass"><i class="fa fa-eye"></i></b-button>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
        <b-form-group class="w-100 mt-1" v-if="loginState == 'new'">
          <b-input-group>
            <b-input class="fm-input" placeholder="Konfirmasi Password" :type="(showpass) ? 'text' : 'password'" v-model="form.data.cpassword"/>
            <b-input-group-append>
              <b-button @click="showpass = !showpass"><i class="fa fa-eye"></i></b-button>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
        <b-button type="submit" variant="default" class="fm-btn mt-3 text-light" :disabled="form.loading">
          <b-spinner small variant="light" label="Spinning" v-show="form.loading" class="mr-2"/> SIGN IN
        </b-button>
        <!-- <router-link to="/forgot-password" class="mt-1 pg-link">Forgot Password?</router-link> -->
      </form>
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
  name: 'Login',
  data() {
    return {
      app :settings,
      account: [
        {
          cif_no : 999990000000818,
          pass : 123,
          status : 1
        },
        {
          cif_no : 999990000000817,
          status : 2
        }
      ],
      form : {
        data : {
          id_user: '0101010105',
          // id_user: null,
          password: null,
          tipe_user: null,
          token: null
        },
        loading : null
      },
      loginState : null,
      showpass: false
    }
  },
  watch: {
    user(val){
      let user = val
      if(user && user.token && user.cif_no){
        this.$router.push("/");
      }
    }
  },
  computed: {
    ...mapGetters(["user"])
  },
  methods: {
    ...mapActions(["login"]),
    doLogin(){
      let url = `${baseUrl}auth/check_username`
      switch (this.loginState) {
        case 'registered':
          url = `${baseUrl}auth/check_password`
          break;
        case 'new':
          url = `${baseUrl}auth/check_password`
          break;
      }
      if((this.loginState == 'new' && this.password == this.cpassword) || (this.loginState == 'registered') || (!this.loginState)){
        let payload = new FormData()
        for(let key in this.form.data){
          payload.append(key,this.form.data[key])
        }
        if(this.loginState && !this.form.data.password){
          this.notif('Warning','Silahkan masukan Password','warning')
        } else {
          this.form.loading = true
          axios
          .post(url,payload)
          .then((res)=>{
            const { data,msg,status,token } = res.data
            switch (msg) {
              case 'Silakkan masukkan Password Anda':
                this.notif('Success',msg,'success')
                this.form.data.tipe_user = data.tipe_user
                if(token)
                  this.form.data.token = token
                if(this.loginState){
                  let user = {...this.form.data}
                  this.login(user)
                } else {
                  this.loginState = 'registered'
                }
                break;
              case 'Login Berhasil! Anda akan dialihkan ke halaman Dashboard':
              this.notif('Success',msg,'success')
                this.form.data.tipe_user = data.tipe_user
                if(token)
                  this.form.data.token = token
                if(this.loginState){
                  let user = {...this.form.data}
                  this.login(user)
                  this.$router.push("/").catch(()=>{});
                } else {
                  this.loginState = 'new'
                }
                break;
              default:
                this.notif('Warning',msg,'warning')
                break;
            }
            this.form.loading = false
          })
          .catch((res)=>{
            this.form.loading = false
            this.notif('Error',res.message,'danger')
          })
        }
      } else {
        this.notif('Warning','Konfirmasi password tidak sama','warning')
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
  }
}
</script>