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
      <div class="form-group">
        <b-form-select v-model="selectedRembug" :options="rembug" @change="getAnggota(selectedRembug)"></b-form-select>
      </div>
      <div class="form-group">
        <b-form-select v-model="selectedAnggota" :options="anggota" @change="getSaldo(selectedRembug,selectedAnggota.noanggota)"></b-form-select>
      </div>
      <div v-if="selectedAnggota.nama">
        <div class="pg-profile-box d-flex align-items-center justify-content-between w-100 color-2 flex-column mb-3">
          <div class="d-flex w-100">
            <img src="/assets/images/profile.png" alt="">
            <div class="pg-profile-box-text">
              <h2>{{selectedAnggota.nama}}</h2>
              <h3>{{selectedAnggota.noanggota}}</h3>
              <h3>{{selectedAnggota.majelis}} <small>({{selectedAnggota.desa}})</small></h3>
            </div>
          </div>
          <div class="pg-dashboard-nav small">
            <b-row>
              <b-col cols="12">
                <router-link :to="`#`">
                  <div class="py-2">
                    <span>Simpok</span>
                    Rp {{saldo.simpok}}
                  </div>
                </router-link>
              </b-col>
              <b-col cols="6">
                <router-link :to="`/saldo/simwam/${selectedAnggota.noanggota}`" class="color-1">
                  <div class="py-2">
                    <span>Simwa Minggon</span>
                    Rp {{saldo.simwa}}
                  </div>
                </router-link>
              </b-col>
              <b-col cols="6">
                <router-link :to="`/saldo/umroh/${selectedAnggota.noanggota}`" class="color-4">
                  <div class="py-2">
                    <span>Umroh</span>
                    Rp {{saldo.umroh}}
                  </div>
                </router-link>
              </b-col>
              <b-col cols="6">
                <router-link :to="`/saldo/simwa/${selectedAnggota.noanggota}`" class="color-1">
                  <div class="py-2">
                    <span>Simwa 4%</span>
                    Rp {{saldo.simwa4}}
                  </div>
                </router-link>
              </b-col>
              <b-col cols="6">
                <router-link :to="`/saldo/qurban/${selectedAnggota.noanggota}`" class="color-2">
                  <div class="py-2">
                    <span>Rumah</span>
                    Rp {{saldo.qurban}}
                  </div>
                </router-link>
              </b-col>
              <b-col cols="6">
                <router-link :to="`/saldo/sukarela/${selectedAnggota.noanggota}`" class="color-2">
                  <div class="py-2">
                    <span>Sukarela</span>
                    Rp {{saldo.sukarela}}
                  </div>
                </router-link>
              </b-col>
              <b-col cols="6">
                <router-link :to="`/saldo/pendidikan/${selectedAnggota.noanggota}`">
                  <div class="py-2">
                    <span>Pendidikan</span>
                    Rp {{saldo.pendidikan}}
                  </div>
                </router-link>
              </b-col>
              <b-col cols="6">
                <router-link :to="`/saldo/pembiayaan/${selectedAnggota.noanggota}`" class="color-4">
                  <div class="py-2">
                    <span>Pembiayaan</span>
                    Rp {{saldo.saldo_outstanding}}
                  </div>
                </router-link>
              </b-col>
              <b-col cols="6">
                <router-link :to="`/saldo/tabungan-berjangka/${selectedAnggota.noanggota}`" class="color-3">
                  <div class="py-2">
                    <span>Berjangka</span>
                    Rp {{saldo.saldo_deposito}}
                  </div>
                </router-link>
              </b-col>
            </b-row>
          </div>
        </div>
      </div>
      <div class="alert alert-info" v-else>Silahkan pilih rembug dan anggota</div>
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
      anggota: [],
      rembug: [],
      selectedRembug: null,
      selectedAnggota: Object,
      saldo: Object,
      loading: false
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
    async getMajelis(){
      this.rembug = [{
        value: null,
        text: 'Pilih Rembug'
      }]
      let url = `${baseUrl}information/rembug`
      let config = {
        headers: {
          'token': this.user.token
        }
      }
      let resrembug = await axios.get(url,config)
      let {data} = resrembug.data
      data.map((item) => {
        if(item.majelis){
          let opt = {
            value: item.majelis,
            text: item.majelis
          }
          this.rembug.push(opt)
        }
      })
    },
    async getAnggota(rembug){
      if(!rembug) {
        rembug = this.$route.params.rembug
        this.selectedRembug = rembug
      } else {
        this.$router.push(`/anggota/${rembug}`)
      }
      let url = `${baseUrl}information/member`
      let config = {
        headers: {
          'token': this.user.token
        }
      }
      let payload = new FormData()
      payload.append('majelis',rembug)
      this.loading = true
      try {
        this.anggota = []
        let resAnggota = await axios.post(url,payload,config)
        let {data} = resAnggota.data
        data.map((item) => {
          this.anggota.push({
            value: item,
            text: item.nama
          })
        })
        this.loading = false
      } catch (error) {
        this.loading = false
      }
    },
    async getSaldo(rembug,anggota){
      if(!rembug && !anggota) {
        rembug = this.$route.params.rembug
        anggota = this.$route.params.anggota
        this.selectedRembug = rembug
        this.anggota.map((item) => {
          if(item.value.noanggota == anggota){
            this.selectedAnggota = item.value
          }
        })
      } else {
        this.$router.push(`/anggota/${rembug}/${anggota}`)
      }
      let config = {
        headers: {
          'token': this.user.token
        }
      }
      this.saldo = Object
      let payload = new FormData()
      payload.append('noanggota',anggota)
      try {
        let resSaldo = await axios.post(`${baseUrl}information/saldo_member`,payload,config)
        this.saldo = resSaldo.data.data 
      } catch (error) {
        console.log(error)
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
    this.getMajelis()
    if(this.$route.params.rembug){
      this.getAnggota()
    }
    if(this.$route.params.anggota){
      if(this.anggota.length > 0){
        this.getSaldo()
      } else {
        setTimeout(() => {
          this.getSaldo()
        }, 1000);
      }
    }
  }
}
</script>