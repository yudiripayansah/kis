<template>
  <div class="pg-dashboard pg-page">
    <div class="pg-header pt-5 pb-3">
      <img :src="app.koperasi_logo" alt="">
      <!-- <h1>Mobile {{app.koperasi_name}}</h1> -->
      <h6>Transaksi Kirim</h6>
      <div class="pg-header-nav-btn">
        <router-link to="/profile">
          <i class="fas fa-cog"></i>
        </router-link>
      </div>
    </div>
    <div class="pg-content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <b-input-group class="pg-input-group">
          <template #prepend>
            <b-input-group-text>
              <i class="fas fa-credit-card"></i>
            </b-input-group-text>
          </template>
          <b-input placeholder="Pilih Rekening Tujuan" v-model="transaction.data.cif_no"/>
        </b-input-group>
        <label for="scan" class="btn pg-btn-scan">
          <i class="fas fa-qrcode"></i>
          <span>Scan</span>
        </label>
        <qrcode-capture @decode="onReadQr" hidden id="scan"/>
      </div>
      <b-form-group>
        <b-input placeholder="Jumlah Transfer" v-model="transaction.data.amount"/>
      </b-form-group>
      <b-form-group>
        <b-textarea placeholder="Keterangan" v-model="transaction.data.description" no-resize rows="3"/>
      </b-form-group>
      <b-button variant="warning" class="mt-3" block @click="doInq()" :disabled="transaction.loading">
        <b-spinner small variant="warning" label="Spinning" v-show="transaction.loading" class="mr-2"/>
        Kirim
      </b-button>
    </div>
    <b-modal id="pg-modal-send-confirm" title="Konfirmasi Transfer" hide-footer centered header-bg-variant="pg-primary">
      <p>
        Anda akan mentransfer dana dengan detil sebagai berikut
      </p>
      <div class="pg-trf-detail">
        <div class="d-flex align-items-center justify-content-between">
          <span>Tanggal</span><span>{{getdate()}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Nama</span><span>{{transaction.inq.receiver_name}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Cif No</span><span>{{transaction.inq.receiver}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Branch</span><span>{{transaction.inq.branch_name}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Majelis</span><span>{{transaction.inq.cm_name}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Jumlah</span><span>Rp {{thousand(transaction.inq.amount)}}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <span>Keterangan</span><span>{{transaction.inq.description}}</span>
        </div>
      </div>
      <b-form-group v-show="transaction.confirm" class="my-2">
        <b-input v-model="transaction.inq.password" placeholder="Masukan Password" type="password"/>
      </b-form-group>
      <div class="d-flex justify-content-end align-items-center mt-3">
        <b-button size="sm" variant="secondary" @click="$bvModal.hide('pg-modal-send-confirm')" class="mr-2" :disabled="transaction.loading">Batal</b-button>
        <b-button size="sm" variant="pg-primary" @click="(!transaction.confirm) ? transaction.confirm = true : doSend()" :disabled="transaction.loading">
          <b-spinner small variant="light" label="Spinning" v-show="transaction.loading" class="mr-2"/>
          {{(transaction.confirm) ? 'Proses' : 'Kirim'}}
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
          description : null
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
          branch_name : null,
          description : null
        },
        confirm : false
      }
    }
  },
  methods : {
    onReadQr(decoded){
      console.log(decoded)
      this.transaction.data.cif_no = decoded 
    },
    doInq(){
      let url = `${baseUrl}/m_get_inquiry`
      let payloadData = this.transaction.data
      payloadData.token = this.user.token
      if(payloadData.cif_no && payloadData.token && payloadData.amount){
        if(payloadData.cif_no != this.user.cif_no){
          this.transaction.loading = true
          let payload = new FormData()
          for(let key in payloadData){
            payload.append(key,payloadData[key])
          }
          axios
          .post(url,payload)
          .then((res)=>{
            this.transaction.loading = false
            if(res.data.cif_no){
              this.$bvModal.show('pg-modal-send-confirm')
              this.transaction.inq = {
                sender : this.user.cif_no,
                sender_name : this.user.nama,
                receiver : res.data.cif_no,
                receiver_name : res.data.name,
                amount : this.transaction.data.amount,
                password : null,
                token : this.user.token,
                cm_name : res.data.cm_name,
                branch_name : res.data.branch_name,
                description : this.transaction.data.description
              }
            } else {
              this.notif('Warning',res.data.message,'warning')
            }
          })
          .catch((res)=>{
            this.transaction.loading = false
            this.notif('Error',res.message,'danger')
          })
        } else {
          this.notif('Warning','Rekening tujuan tidak valid','warning')
        }
      } else {
        this.notif('Warning','Masukan rekening tujuan dan nominal transfer','warning')
      }
    },
    doSend(){
      if(this.transaction.inq.password == this.user.password){
        let url = `${baseUrl}/m_transfer`
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
    getdate() {
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();

      return today = mm + '/' + dd + '/' + yyyy;
    }
  }
}
</script>