<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Transaksi Top Up</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content">
      <b-form-group>
        <b-input placeholder="Jumlah TopUp" v-model="transaction.data.amount"/>
      </b-form-group>
      <b-button variant="warning" class="mt-3" block @click="doInq()" :disabled="transaction.loading">
        <b-spinner small variant="warning" label="Spinning" v-show="transaction.loading" class="mr-2"/>
        Top UP
      </b-button>
    </div>
    <b-modal id="pg-modal-send-confirm" title="Konfirmasi Transfer" hide-footer centered header-bg-variant="pg-primary">
      <p>
        Detil Top Up Dompet {{app.koperasi_name}}
      </p>
      <div class="pg-trf-detail pg-topup-detail">
        <div class="d-flex align-items-center justify-content-between">
          <span>Cif No</span><span>{{transaction.inq.cif_no}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Saldo Sukarela</span><span>Rp {{thousand(transaction.data.sukarela)}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Total Kewajiban</span><span>Rp {{thousand(transaction.data.financing)}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Saldo Tersedia</span><span>Rp {{thousand(transaction.data.saldo_balance)}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Top Up</span><span>Rp {{thousand(transaction.inq.amount)}}</span>
        </div>
      </div>
      <b-form-group v-show="transaction.confirm" class="my-2">
        <b-input v-model="transaction.inq.password" placeholder="Masukan Password" type="password"/>
      </b-form-group>
      <div class="d-flex justify-content-end align-items-center mt-3">
        <b-button size="sm" variant="secondary" @click="$bvModal.hide('pg-modal-send-confirm')" class="mr-2" :disabled="transaction.loading">Batal</b-button>
        <b-button size="sm" variant="pg-primary" @click="doProcess()" :disabled="transaction.loading">
          <b-spinner small variant="light" label="Spinning" v-show="transaction.loading" class="mr-2"/>
          {{(transaction.confirm) ? 'Proses' : 'Top Up'}}
        </b-button>
      </div>
    </b-modal>
  </div>
</template>
<script>
import axios from 'axios'
import {baseUrl,settings} from '../config'
import {
  mapGetters
} from "vuex";
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'
export default {
  components: {
    QrcodeStream,
    QrcodeDropZone,
    QrcodeCapture
  },
  computed: {
    ...mapGetters(["user"])
  },
  data() {
    return {
      app :settings,
      transaction : {
        loading : false,
        data : {
          cif_no : null,
          token : null,
          amount : null,
          saldo_balance : null,
          financing : null,
          sukarela : null,
          saving : null
        },
        inq : {
          cif_no : null,
          token : null,
          amount : null,
          password : null
        },
        confirm : false
      }
    }
  },
  methods : {
    doInq(){
      let url = `${baseUrl}/m_info_topup`
      let payloadData = {
        token : this.user.token,
        cif_no : this.user.cif_no,
        amount : this.transaction.data.amount
      }
      if(payloadData.cif_no && payloadData.token && payloadData.amount){
        this.transaction.loading = true
        let payload = new FormData()
        for(let key in payloadData){
          payload.append(key,payloadData[key])
        }
        axios
        .post(url,payload)
        .then((res)=>{
          this.transaction.loading = false
          this.$bvModal.show('pg-modal-send-confirm')
          this.transaction.data.saldo_balance = res.data.saldo_balance.replace('Rp.','').replace(',','').replace(',','')
          this.transaction.data.financing = res.data.financing.replace('Rp.','').replace(',','').replace(',','')
          this.transaction.data.sukarela = res.data.sukarela.replace('Rp.','').replace(',','').replace(',','')
          this.transaction.data.saving = res.data.saving.replace('Rp.','').replace(',','').replace(',','')
          this.transaction.data.saldo_balance = (this.transaction.data.saldo_balance < 0) ? 0 : this.transaction.data.saldo_balance
          this.transaction.inq = {
            cif_no : this.user.cif_no,
            token : this.user.token,
            amount : this.transaction.data.amount,
            password : null
          }
        })
        .catch((res)=>{
          this.transaction.loading = false
          this.notif('Error',res.message,'danger')
        })
      } else {
        this.notif('Warning','Masukan rekening tujuan dan nominal transfer','warning')
      }
    },
    doProcess(){
      if(!this.transaction.confirm){
        console.log(this.transaction.inq.amount)
        console.log(this.transaction.data.saldo_balance)
        if(Number(this.transaction.data.saldo_balance) >= Number(this.transaction.inq.amount)) {
          this.transaction.confirm = true
        } else {
          this.transaction.confirm = false 
          this.notif('Warning','Saldo Tidak Mencukupi','warning')
        }
      } else {
        this.doTopUp()
      }
    },
    doTopUp(){
      if(this.transaction.inq.password == this.user.password){
        let url = `${baseUrl}/m_topup`
        let payloadData = this.transaction.inq
        let payload = new FormData()
          for(let key in payloadData){
            payload.append(key,payloadData[key])
          }
          this.transaction.loading = true
          axios
          .post(url,payload)
          .then((res)=>{
            if(res.data.status == 1){
              this.clearForm()
              this.$bvModal.hide('pg-modal-send-confirm')
              this.notif('Success',res.data.message,'success')
            } else {
              this.notif('Error',res.data.message,'danger')
            }
            this.transaction.loading = false
          })
          .catch((e)=>{
            this.transaction.loading = false
            this.notif('Error',e.message,'danger')
          })
      } else {
        this.notif('Warning','Password yang anda masukan salah','warning')
      }
    },
    clearForm() {
      this.transaction = {
        loading : false,
        data : {
          cif_no : null,
          token : null,
          amount : null
        },
        inq : {
          sender : null,
          sender_name : null,
          reciever : null,
          reciever_name : null,
          amount : null,
          password : null,
          token : null,
          cm_name : null,
          branch_name : null
        },
        confirm : false
      }
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
  }
}
</script>