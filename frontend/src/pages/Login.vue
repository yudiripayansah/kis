<template>
  <div class="pg-login pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <h1>Login</h1>
    </div>
    <div class="pg-content">
      <form @submit.prevent="doLogin" class="d-flex flex-column align-items-center justify-content-center w-100">
        <b-form-group class="w-100">
          <b-input class="fm-input" placeholder="Username / Userid" v-model="form.data.cif_no"/>
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
        <b-button type="submit" variant="default" class="fm-btn mt-3" :disabled="form.loading">
          <b-spinner small variant="light" label="Spinning" v-show="form.loading" class="mr-2"/> SIGN IN
        </b-button>
        <router-link to="/forgot-password" class="mt-1 pg-link">Forgot Password?</router-link>
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
// 999990000000818
// 999990008717
// 103000219001315
// 80020114004515
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
          cif_no : null,
          password : null,
          cpassword : null,
          status : null,
          token : null
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
      let url = `${baseUrl}/m_check_username`
      switch (this.loginState) {
        case 'registered':
          url = `${baseUrl}/m_check_password`
          break;
        case 'new':
          url = `${baseUrl}/m_check_password`
          break;
      }
      if((this.loginState == 'new' && this.password == this.cpassword) || (this.loginState == 'registered') || (!this.loginState)){
        let payload = new FormData()
        for(let key in this.form.data){
          if(key != 'token')
            payload.append(key,this.form.data[key])
        }
        if(this.loginState && !this.form.data.password){
          this.notif('Warning','Silahkan masukan Password','warning')
        } else {
          this.form.loading = true
          axios
          .post(url,payload)
          .then((res)=>{
            let theMsg = res.data.message
            switch (res.data.status) {
              case '1':
                this.notif('Success',theMsg,'success')
                this.form.data.status = res.data.status
                if(res.data.token)
                  this.form.data.token = res.data.token
                if(this.loginState){
                  let user = {...this.form.data,nama:res.data.nama,saldo:res.data.saldo,cif_type:res.data.cif_type}
                  this.login(user)
                }else
                  this.loginState = 'registered'
                break;
              case '2':
                this.notif('Success',theMsg,'success')
                this.form.data.status = res.data.status
                if(res.data.token)
                  this.form.data.token = res.data.token
                if(this.loginState){
                  let user = {...this.form.data,nama:res.data.nama,saldo:res.data.saldo}
                  this.login(user)
                }else
                  this.loginState = 'new'
                break;
              case '3':
                this.notif('Warning',theMsg,'warning')
                break;
              case '4':
                this.notif('Warning',theMsg,'warning')
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
    checkLogin(){
      if(this.user && this.user.token && this.user.cif_no){
        this.$router.push("/");
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
    this.checkLogin()
  }
}
</script>