<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6 class="text-capitalize">Statement Pembiayaan</h6>
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
            <b-input-group>
              <template #prepend>
                <b-input-group-text>
                  <i class="fas fa-credit-card"></i>
                </b-input-group-text>
              </template>
              <b-input placeholder="No Rekening" v-model="user.cif_no" disabled/>
            </b-input-group>
          </b-form-group>
        </b-col>
        <b-col xs=6>
          <b-form-group>
            <b-form-datepicker placeholder="From" :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }" locale="id" v-model="pembiayaan.from" @input="doGet()"/>
          </b-form-group>
        </b-col>
        <b-col xs=6>
          <b-form-group>
            <b-form-datepicker placeholder="To" :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }" locale="id" v-model="pembiayaan.to" @input="doGet()"/>
          </b-form-group>
        </b-col>
      </b-row>
      <b-overlay :show="pembiayaan.loading">
        <div v-if="pembiayaan.data.length > 0">
          <div class="pg-saldo-pembiayaan mt-3" v-for="(tr,trIndex) in pembiayaan.data" :key="`saldo-${trIndex}`" :class="(tr.tanggal_bayar) ? 'green' : null">
            <h1>Angsuran Ke {{tr.angsuran_ke}}</h1>
            <div class="font-12 d-flex justify-content-between align-items-center">
              <label>Tanggal Bayar</label>
              <span>{{(tr.tanggal_bayar) ? tr.tanggal_bayar : 'Belum dibayar'}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-center">
              <label>Angsuran</label>
              <span>{{tr.angsuran}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-center">
              <label>Saldo Pokok</label>
              <span>{{tr.saldo_pokok}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-center">
              <label>Saldo Margin</label>
              <span>{{tr.saldo_margin}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-center">
              <label>Saldo Catab</label>
              <span>{{tr.saldo_catab}}</span>
            </div>
          </div>
        </div>
        <div v-else>
          <b-alert variant="warning" :show="!pembiayaan.loading">Tidak ada transaksi pada rentan waktu ini</b-alert>
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
      pembiayaan : {
        data : [],
        loading : false,
        from : new Date('01/01/2021'),
        to : new Date('12/31/2021'),
      }
    }
  },
  computed: {
    ...mapGetters(["user"])
  },
  methods : {
    doGet(){
      this.pembiayaan.loading = true
      let url = `${baseUrl}/m_statement_installment`
      let payloadData = {
        account_financing_no : this.$route.params.account_financing_no,
        cif_no : this.user.cif_no,
        token : this.user.token,
        from : this.formatDate(this.pembiayaan.from),
        thru : this.formatDate(this.pembiayaan.to),
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      axios
      .post(url,payload)
      .then((res)=>{
        this.pembiayaan.loading = false
        this.pembiayaan.data = []
        if(res.data[0].amount != 'TIDAK ADA DATA') {
          this.pembiayaan.data = res.data
        }
      })
      .catch((res)=>{
        this.pembiayaan.loading = false
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
  },
  mounted(){
    this.doGet()
  }
}
</script>