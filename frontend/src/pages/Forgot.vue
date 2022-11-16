<template>
  <div class="pg-login pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <h1>Forgot</h1>
    </div>
    <div class="pg-content">
      <form @submit.prevent="doForgot" class="d-flex flex-column align-items-center justify-content-center w-100">
        <b-form-group class="w-100">
          <b-input class="fm-input" placeholder="Username / Userid" v-model="form.data.username"/>
        </b-form-group>
        <b-button type="submit" variant="default" class="fm-btn mt-3 text-light" :disabled="form.loading">
          <b-spinner small variant="light" label="Spinning" v-show="form.loading" class="mr-2"/> SUBMIT
        </b-button>
        <router-link to="/login" class="mt-1 pg-link">Login Here</router-link>
      </form>
    </div>
  </div>
</template>
<script>
import axios from 'axios'
import {baseUrl,settings} from '../config'
export default {
  name: 'Forgot',
  data() {
    return {
      app :settings,
      form : {
        data : {
          username: '0101010105',
        },
        loading : null
      }
    }
  },
  methods: {
    async doForgot(){
      let url = `${baseUrl}auth/forgot_password`
      try {
        let payload = new FormData()
        payload.append('username',this.form.data.username)
        let res = await axios.post(url,payload)
        if(res.data.status){
          const {data,msg,status} = res.data
          console.log(res)
          this.notif('success',msg,'success')
        } else {
          this.notif('error',res.data.msg,'error')
        }
      } catch (error) {
        this.notif('error',error,'error')
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