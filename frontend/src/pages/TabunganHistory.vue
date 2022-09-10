<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6 class="text-capitalize">Statement {{ $route.params.product_name }}</h6>
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
            <b-form-datepicker placeholder="From" :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }" locale="id" v-model="tabungan.from" @input="doGet()"/>
          </b-form-group>
        </b-col>
        <b-col xs=6>
          <b-form-group>
            <b-form-datepicker placeholder="To" :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }" locale="id" v-model="tabungan.to" @input="doGet()"/>
          </b-form-group>
        </b-col>
      </b-row>
      <b-overlay :show="tabungan.loading">
        <div v-if="tabungan.data.length > 0">
          <div class="pg-saldo-pembiayaan mt-3" v-for="(tr,trIndex) in tabungan.data" :key="`saldo-${trIndex}`" :class="(tr.type == 'Kredit') ? 'green' : null">
            <div class="font-12 d-flex justify-content-between align-items-center">
              <label>{{tr.transaction_date}}</label>
            </div>
            <h1>{{tr.description}}</h1>
            <div class="font-12 d-flex justify-content-between align-items-center">
              <span>{{tr.type}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-center">
              <label>Nominal</label>
              <span>{{tr.amount}}</span>
            </div>
            <div class="font-12 d-flex justify-content-between align-items-center">
              <label>Saldo</label>
              <span>{{tr.saldo}}</span>
            </div>
          </div>
        </div>
        <div v-else>
          <b-alert variant="warning" :show="!tabungan.loading">Tidak ada transaksi pada rentan waktu ini</b-alert>
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
      app :settings,
      tabungan : {
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
      this.tabungan.loading = true
      let url = `${baseUrl}/m_statement_tabungan`
      let payloadData = {
        product_name : this.$route.params.product_name,
        account_saving_no : this.$route.params.account_saving_no,
        cif_type : this.$route.params.cif_type,
        token : this.user.token,
        from_date : this.formatDate(this.tabungan.from),
        thru_date : this.formatDate(this.tabungan.to),
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      axios
      .post(url,payload)
      .then((res)=>{
        this.tabungan.loading = false
        this.tabungan.data = []
        if(res.data[0].amount != 'TIDAK ADA DATA') {
          res.data.map((x) => {
            let type = x.description.split(' ')
            if(type[0] == 'PENARIKAN')
              x.type = 'Debet'
            else
              x.type = 'Kredit'
            this.tabungan.data.push(x)
          })
        }
      })
      .catch((res)=>{
        this.tabungan.loading = false
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