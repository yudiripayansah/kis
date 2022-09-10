<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6 class="text-capitalize">Pengajuan</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content pg-content-histori-transaksi">
      <b-row>
        <b-col sm=12>
          <b-form-group>
            <router-link class="btn btn-primary btn-block d-flex align-items-center justify-content-center" to="/pengajuan/form">Buat Pengajuan Baru</router-link>
          </b-form-group>
        </b-col>
        <!-- <b-col xs=6>
          <b-form-group>
            <b-form-datepicker placeholder="From" :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }" locale="id" v-model="pengajuan.from" @input="doGet()"/>
          </b-form-group>
        </b-col>
        <b-col xs=6>
          <b-form-group>
            <b-form-datepicker placeholder="To" :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }" locale="id" v-model="pengajuan.to" @input="doGet()"/>
          </b-form-group>
        </b-col> -->
      </b-row>
      <b-overlay :show="pengajuan.loading">
        <div v-if="pengajuan.data.length > 0">
          <div class="pg-saldo-pembiayaan mt-3" v-for="(tr,trIndex) in pengajuan.data" :key="`saldo-${trIndex}`" :class="(tr.status == 'Lunas') ? 'green' : null">
            <h1>Pengajuan Ke {{tr.pembiayaan_ke}}</h1>
            <div class="font-12 d-flex justify-content-between align-items-start">
              <label>Tanggal Pengajuan</label>
              <span>{{formatDate(tr.tanggal_pengajuan)}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-start">
              <label>Jumlah</label>
              <span>Rp {{ thousand(tr.jumlah) }}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-start">
              <label>Peruntukan</label>
              <span>{{tr.peruntukan}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-start">
              <label>Keterangan</label>
              <span>{{tr.keterangan}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-start">
              <label>Tanggal Pencairan</label>
              <span>{{formatDate(tr.rencana_pencairan)}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-start">
              <label>Status</label>
              <span>{{tr.status}}</span>
            </div>
          </div>
        </div>
        <div v-else>
          <b-alert variant="warning" :show="!pengajuan.loading">Belum ada pengajuan pembiayaan</b-alert>
        </div>
      </b-overlay>
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
      pengajuan : {
        data : [],
        loading : false,
        from : new Date('01/01/2021'),
        to : new Date('12/31/2021'),
      },
      keterangan : [
        'Modal kerja',
        'Investasi',
        'Pendidikan',
        'Perumahan',
        'Kesehatan',
        'Aset',
        'Lain-Lain',
        'Air bersih & Sanitasi',
      ]
    }
  },
  computed: {
    ...mapGetters(["user"])
  },
  methods : {
    doGet(){
      this.pengajuan.loading = true
      let url = `${baseUrl}/m_list_submission`
      let payloadData = {
        cif_no : this.user.cif_no,
        token : this.user.token
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      axios
      .post(url,payload)
      .then((res)=>{
        console.log(res)
        this.pengajuan.loading = false
        this.pengajuan.data = []
        if(res.data.length > 0){
          this.pengajuan.data = res.data
        }
      })
      .catch((res)=>{
        this.pengajuan.loading = false
        this.notif('Error',res.message,'danger')
      })
    },
    formatDate(date){
      var dateObj = new Date(date);
      var month = dateObj.getUTCMonth() + 1; //months from 1-12
      var day = dateObj.getUTCDate();
      var year = dateObj.getUTCFullYear();

      return day + "/" + month + "/" + year;
    },
    notif(title,msg,type){
      this.$bvToast.toast(msg, {
        title: title,
        autoHideDelay: 5000,
        variant: type,
        toaster: 'b-toaster-bottom-center'
      })
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
  },
  mounted(){
    this.doGet()
  }
}
</script>