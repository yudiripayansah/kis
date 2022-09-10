<template>
  <div class="pg-login pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <h1>Forgot Password</h1>
    </div>
    <div class="pg-content">
      <form @submit.prevent="doForgot" class="d-flex flex-column align-items-center justify-content-center w-100">
        <b-form-group class="w-100">
          <b-input class="fm-input" placeholder="Username/ No Anggota" v-model="form.data.cif_no"/>
        </b-form-group>
        <b-button type="submit" variant="default" class="fm-btn mt-3" :disabled="form.loading">
          <b-spinner small variant="light" label="Spinning" v-show="form.loading" class="mr-2"/> SUBMIT
        </b-button>
        <router-link to="/login" class="mt-1 pg-link">Login here?</router-link>
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
    doForgot(){
      let url = `${baseUrl}/m_forgot_password`
        let payload = new FormData()
        for(let key in this.form.data){
          if(key != 'token')
            payload.append(key,this.form.data[key])
        }
        this.form.loading = true
        axios
        .post(url,payload)
        .then((res)=>{
          let theMsg = res.data.message
          let status = res.data.status
          if(status == 1){
            this.notif('Success',theMsg,'success')
            this.form.data.cif_no = null
          }
          this.form.loading = false
        })
        .catch((res)=>{
          this.form.loading = false
          this.notif('Error',res.message,'danger')
        })
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