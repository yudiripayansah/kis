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
      <div class="pg-step-container">
        <div>
          <span>{{pengajuan.step}}</span> <h6>{{pengajuan.titleStep[pengajuan.step]}}</h6>
        </div>
        <div>
          <h5 v-for="n in 4" :key="`ms-${n}`" v-show="pengajuan.step <= Number(n)">{{Number(n)+1}}</h5>
        </div>
      </div>
      <div class="pg-step" v-show="pengajuan.step == 1">
        <h4 class="mb-3 pb-3 border-bottom">Data Pribadi</h4>
        <b-form-group label="No Anggota">
          <b-input v-model="profile.cif_no" disabled/>
        </b-form-group>
        <b-form-group label="Nama Lengkap">
          <b-input v-model="profile.name" disabled/>
        </b-form-group>
        <b-form-group label="No KTP">
          <b-input v-model="profile.id_card" disabled/>
        </b-form-group>
        <b-form-group label="Alamat">
          <b-input v-model="profile.address" disabled/>
        </b-form-group>
        <b-form-group label="Rembug">
          <b-input v-model="profile.cm_name" disabled/>
        </b-form-group>
        <h4 class="pt-3 mb-3 pb-3 border-bottom">Data Pengajuan</h4>
        <b-form-group label="No Pengajuan">
          <b-input v-model="pengajuan.data.map_no" disabled/>
        </b-form-group>
        <b-form-group label="Jenis Pembiayaan">
          <b-select v-model="pengajuan.data.financing_type" :options="opt.financing_type"/>
        </b-form-group>
        <b-form-group label="Pembiayaan Ke">
          <b-input v-model="pengajuan.data.pyd" disabled/>
        </b-form-group>
        <b-form-group label="Jumlah Pengajuan">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.amount" class="form-control"/>
        </b-form-group>
        <b-form-group label="Periode">
          <b-select v-model="pengajuan.data.periode_jangka_waktu" :options="opt.periode_jangka_waktu" @change="setJangkawaktu()"/>
        </b-form-group>
        <b-form-group label="Jangka Waktu">
          <b-input v-model="pengajuan.data.jangka_waktu" :disabled="pengajuan.data.periode_jangka_waktu == 3"/>
        </b-form-group>
        <b-form-group label="Peruntukan">
          <b-select v-model="pengajuan.data.peruntukan" :options="opt.peruntukan"/>
        </b-form-group>
        <b-form-group label="Keterangan">
          <b-textarea no-resize rows="5" v-model="pengajuan.data.description"/>
        </b-form-group>
        <b-form-group label="Sumber Pengembalian">
          <b-select v-model="pengajuan.data.sumber_pengembalian" :options="opt.sumber_pengembalian"/>
        </b-form-group>
        <b-form-group label="Tanggal Pengajuan">
          <b-input v-model="pengajuan.data.tanggal_pengajuan" disabled/>
        </b-form-group>
        <b-form-group label="Upload Ktp" description="Tap untuk mengubah gambar">
          <label for="fm-ktp" class="w-100" ref="previewImage">
            <b-img-lazy :src="(pengajuan.data.doc_ktp) ? pengajuan.data.doc_ktp : require('../assets/images/id-front.png')" fluid class="w-100"/>
            <input type="file" ref="fm-ktp" hidden id="fm-ktp"
            @change="previewImage($event,'ktp')" accept="image/*"> 
          </label>
        </b-form-group>
        <b-form-group label="Upload Kartu Keluarga" description="Tap untuk mengubah gambar">
          <label for="fm-kk" class="w-100" ref="previewImage">
            <b-img-lazy :src="(pengajuan.data.doc_kk) ? pengajuan.data.doc_kk : require('../assets/images/id-back.png')" fluid class="w-100"/>
            <input type="file" ref="fm-kk" hidden id="fm-kk"
            @change="previewImage($event,'kk')" accept="image/*"> 
          </label>
        </b-form-group>
        <b-form-group label="Upload Dokumen Pendukung" description="Tap untuk mengubah gambar">
          <label for="fm-doc" class="w-100" ref="previewImage">
            <b-img-lazy :src="(pengajuan.data.doc_pendukung) ? pengajuan.data.doc_pendukung : require('../assets/images/id-back.png')" fluid class="w-100"/>
            <input type="file" ref="fm-doc" hidden id="fm-doc"
            @change="previewImage($event,'doc')" accept="image/*"> 
          </label>
        </b-form-group>
      </div>
      <div class="pg-step" v-show="pengajuan.step == 2">
        <h4 class="mb-3 pb-3 border-bottom">Jenis Usaha</h4>
        <b-form-group label="Jenis Usaha">
          <b-input v-model="pengajuan.data.jenis_usaha"/>
        </b-form-group>
        <b-form-group label="Komoditi">
          <b-input v-model="pengajuan.data.komoditi"/>
        </b-form-group>
        <b-form-group label="Lama Usaha (Dalam Tahun)">
          <b-input v-model="pengajuan.data.lama_usaha"/>
        </b-form-group>
        <b-form-group label="Lokasi Usaha">
          <b-input v-model="pengajuan.data.lokasi_usaha"/>
        </b-form-group>
        <b-form-group label="Surat Izin Usaha">
          <b-input v-model="pengajuan.data.surat_ijin_usaha"/>
        </b-form-group>
        <b-form-group label="Aset Usaha">
          <b-input v-model="pengajuan.data.aset_usaha"/>
        </b-form-group>
        <b-form-group label="Nilai Aset">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.nilai_aset" class="form-control"/>
        </b-form-group>
      </div>
      <div class="pg-step" v-show="pengajuan.step == 3">
        <h4 class="mb-3 pb-3 border-bottom">Modal Usaha</h4>
        <b-form-group label="Persediaan Awal">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.persediaan_awal" class="form-control"/>
        </b-form-group>
        <b-form-group label="Belanja/ Pembelian">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.belanja_pembelian" class="form-control"/>
        </b-form-group>
        <b-form-group label="Persediaan Akhir">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.persediaan_akhir" class="form-control"/>
        </b-form-group>
        <b-form-group label="HPP">
          <vue-numeric currency="Rp " separator="." :value="Number(pengajuan.data.belanja_pembelian)+Number(pengajuan.data.persediaan_awal)-Number(pengajuan.data.persediaan_akhir)" disabled class="form-control"/>
        </b-form-group>
        <b-form-group label="Omset">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.omset" class="form-control"/>
        </b-form-group>
        <b-form-group label="Laba Kotor">
          <vue-numeric currency="Rp " separator="." :value="Number(pengajuan.data.omset)-Number(pengajuan.data.belanja_pembelian)+Number(pengajuan.data.persediaan_awal)-Number(pengajuan.data.persediaan_akhir)" disabled class="form-control"/>
        </b-form-group>
        <h4 class="pt-3 mb-3 pb-3 border-bottom">Biaya Usaha</h4>
        <b-form-group label="Piutang">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.piutang" class="form-control"/>
        </b-form-group>
        <b-form-group label="Biaya Usaha">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.biaya_usaha" class="form-control"/>
        </b-form-group>
        <b-form-group label="Sewa Tempat">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.sewa_tempat" class="form-control"/>
        </b-form-group>
        <b-form-group label="Total Biaya Usaha">
          <vue-numeric currency="Rp " separator="." :value="Number(pengajuan.data.piutang)+Number(pengajuan.data.biaya_usaha)+Number(pengajuan.data.sewa_tempat)" disabled class="form-control"/>
        </b-form-group>
        <b-form-group label="Keuntungan Usaha">
          <vue-numeric currency="Rp " separator="." :value="(Number(pengajuan.data.omset)-Number(pengajuan.data.belanja_pembelian)+Number(pengajuan.data.persediaan_awal)-Number(pengajuan.data.persediaan_akhir))-(Number(pengajuan.data.piutang)+Number(pengajuan.data.biaya_usaha)+Number(pengajuan.data.sewa_tempat))" disabled class="form-control"/>
        </b-form-group>
      </div>
      <div class="pg-step" v-show="pengajuan.step == 4">
        <h4 class="mb-3 pb-3 border-bottom">Data Anggota</h4>
        <b-form-group label="No Telp Anggota">
          <b-input v-model="pengajuan.data.telepon_anggota"/>
        </b-form-group>
        <b-form-group label="Pekerjaan Anggota">
          <b-select v-model="pengajuan.data.pekerjaan_anggota" :options="opt.pekerjaan"/>
        </b-form-group>
        <b-form-group label="No Telp Pasangan">
          <b-input v-model="pengajuan.data.telepon_pasangan"/>
        </b-form-group>
        <b-form-group label="Pekerjaan Pasangan">
          <b-select v-model="pengajuan.data.pekerjaan_pasangan" :options="opt.pekerjaan"/>
        </b-form-group>
        <h4 class="pt-3 mb-3 pb-3 border-bottom">Pendapatan / Penghasilan</h4>
        <b-form-group label="Pendapatan Gaji">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.pendapatan_gaji" class="form-control"/>
        </b-form-group>
        <b-form-group label="Pendapatan Usaha">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.pendapatan_usaha" class="form-control"/>
        </b-form-group>
        <b-form-group label="Pendapatan Lainnya">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.pendapatan_lainnya" class="form-control"/>
        </b-form-group>
        <b-form-group label="Total Pendapatan">
          <vue-numeric currency="Rp " separator="." :value="Number(pengajuan.data.pendapatan_gaji)+Number(pengajuan.data.pendapatan_usaha)+Number(pengajuan.data.pendapatan_lainnya)" disabled class="form-control"/>
        </b-form-group>
      </div>
      <div class="pg-step" v-show="pengajuan.step == 5">
        <h4 class="mb-3 pb-3 border-bottom">Tanggungan dan Saving Power</h4>
        <b-form-group label="Jumlah Tanggungan">
          <b-input v-model="pengajuan.data.jumlah_tanggungan"/>
        </b-form-group>
        <b-form-group label="Biaya Rumah Tangga">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.biaya_rumah_tangga" class="form-control"/>
        </b-form-group>
        <b-form-group label="Biaya Rekening">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.biaya_rekening" class="form-control"/>
        </b-form-group>
        <b-form-group label="Biaya Kontrakan">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.biaya_kontrakan" class="form-control"/>
        </b-form-group>
        <b-form-group label="Biaya Pendidikan">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.biaya_pendidikan" class="form-control"/>
        </b-form-group>
        <b-form-group label="Hutang Lainnya">
          <vue-numeric currency="Rp " separator="." v-model="pengajuan.data.hutang_lainnya" class="form-control"/>
        </b-form-group>
        <b-form-group label="Total Biaya">
          <vue-numeric currency="Rp " separator="." :value="Number(pengajuan.data.biaya_rumah_tangga)+Number(pengajuan.data.biaya_rekening)+Number(pengajuan.data.biaya_kontrakan)+Number(pengajuan.data.biaya_pendidikan)+Number(pengajuan.data.hutang_lainnya)" disabled class="form-control"/>
        </b-form-group>
        <b-form-group label="Saving Power">
          <vue-numeric currency="Rp " separator="." :value="(Number(pengajuan.data.pendapatan_gaji)+Number(pengajuan.data.pendapatan_usaha)+Number(pengajuan.data.pendapatan_lainnya))-(Number(pengajuan.data.biaya_rumah_tangga)+Number(pengajuan.data.biaya_rekening)+Number(pengajuan.data.biaya_kontrakan)+Number(pengajuan.data.biaya_pendidikan)+Number(pengajuan.data.hutang_lainnya))" disabled class="form-control"/>
        </b-form-group>
        <b-form-group label="Repayment Capacity">
          <vue-numeric currency="Rp " separator="." :value="((Number(pengajuan.data.pendapatan_gaji)+Number(pengajuan.data.pendapatan_usaha)+Number(pengajuan.data.pendapatan_lainnya))-(Number(pengajuan.data.biaya_rumah_tangga)+Number(pengajuan.data.biaya_rekening)+Number(pengajuan.data.biaya_kontrakan)+Number(pengajuan.data.biaya_pendidikan)+Number(pengajuan.data.hutang_lainnya))) * 75/100" disabled class="form-control"/>
        </b-form-group>
        <b-form-group label="Tanda Tangan Nasabah">
          <VueSignaturePad width="100%" height="200px" ref="signaturePad" class="pg-ttd"/>
          <div class="d-flex justify-content-between">
            <b-button variant="secondary" @click="undo(1)" block>Ulangi</b-button>
          </div>
        </b-form-group>
        <b-form-group label="Tanda Tangan Pasangan">
          <VueSignaturePad width="100%" height="200px" ref="signaturePad2" class="pg-ttd"/>
          <div class="d-flex justify-content-between">
            <b-button variant="secondary" @click="undo(2)" block>Ulangi</b-button>
          </div>
        </b-form-group>
        <!-- <b-form-group label="Ttd Anggota">
          <span>{{pengajuan.data.ttd_anggota}}</span>
        </b-form-group>
        <b-form-group label="Ttd Pasangan">
          <span>{{pengajuan.data.ttd_pasangan}}</span>
        </b-form-group> -->
      </div>
      <div class="pg-nav-step d-flex justify-content-center mt-4 pt-4 border-top">
        <b-button variant="secondary" @click="move(pengajuan.step-1)" v-show="pengajuan.step > 1" class="mx-2">Kembali</b-button>
        <b-button variant="primary" @click="move(pengajuan.step+1)" class="mx-2" :disabled="pengajuan.loading">
          <b-spinner small v-show="pengajuan.loading" />
          {{(pengajuan.step==5) ? 'Buat Pengajuan' : 'Selanjutnya'}}
        </b-button>
      </div>
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
      profile : {
        branch_name:  null,
        cm_name: null,
        cif_no: null,
        name:  null,
        saldo: null,
        message: null
      },
      pengajuan : {
        data : {
          cif_no : null,
          amount : 0,
          financing_type : null,
          peruntukan : null,
          rencana_droping : null,
          pembiayaan_ke : 1,
          periode_jangka_waktu: 1,
          jangka_waktu: 0,
          description : null,
          tanggal_pengajuan : null,
          created_by : null,
          pyd : 1,
          map_no : null,
          ttd_anggota : null,
          ttd_pasangan : null,
          sumber_pengembalian : null,
          jenis_usaha : '',
          komoditi : '',
          lama_usaha : '',
          lokasi_usaha : '',
          surat_ijin_usaha : '',
          aset_usaha : '',
          nilai_aset : 0,
          persediaan_awal : 0,
          belanja_pembelian : 0,
          persediaan_akhir : 0,
          hpp: 0,
          omset : 0,
          laba_kotor : 0,
          piutang : 0,
          biaya_usaha : 0,
          sewa_tempat : 0,
          total_biaya_usaha : 0,
          keuntungan_usaha : 0,
          telepon_anggota : null,
          pekerjaan_anggota : null,
          telepon_pasangan : null,
          pekerjaan_pasangan : null,
          pendapatan_gaji : 0,
          pendapatan_usaha : 0,
          pendapatan_lainnya : 0,
          total_pendapatan : 0,
          jumlah_tanggungan : null,
          biaya_rumah_tangga : 0,
          biaya_rekening : 0,
          biaya_kontrakan : 0,
          biaya_pendidikan : 0,
          hutang_lainnya : 0,
          total_biaya : 0,
          saving_power : 0,
          repayment_capacity : 0,
          doc_ktp: null,
          doc_kk: null,
          doc_pendukung: null,
          token : null
        },
        titleStep : ['','Data Pribadi & Data Pengajuan','Memorandum Analisis Pembiayaan 1','Memorandum Analisis Pembiayaan 2','Memorandum Analisis Pembiayaan 3','Memorandum Analisis Pembiayaan 4'],
        step : 1,
        maxstep : 5,
        title : [],
        loading : false
      },
      opt : {
        financing_type : [
          {value: 0, text: 'Kelompok'},
          {value: 1, text: 'Individu'}
        ],
        peruntukan : [
          {value: 1 ,text : 'Modal kerja'},
          {value: 2 ,text : 'Investasi'},
          {value: 3 ,text : 'Pendidikan'},
          {value: 4 ,text : 'Perumahan'},
          {value: 5 ,text : 'Kesehatan'},
          {value: 6 ,text : 'Aset'},
          {value: 9 ,text : 'Lain-Lain'},
          {value: 7 ,text : 'Air bersih & Sanitasi'},
        ],
        sumber_pengembalian : ["sumber usaha","gaji"],
        pekerjaan : [
          {value:0,text: 'Ibu Rumah Tangga'},
          {value:1,text: 'Buruh'},
          {value:2,text: 'Petani'},
          {value:3,text: 'Pedagang'},
          {value:4,text: 'Wiraswasta'},
          {value:5,text: 'Pegawai Negeri Sipil'},
          {value:6,text: 'Karyawan Swasta'},
        ],
        periode_jangka_waktu: [
          {value:1,text: 'Mingguan'},
          {value:2,text: 'Bulanan'},
          {value:3,text: 'Jatuh Tempo'},
        ]
      }
    }
  },
  computed: {
    ...mapGetters(["user"])
  },
  methods : {
    undo(target) {
      if(target == 1)
        this.$refs.signaturePad.undoSignature();
      else
        this.$refs.signaturePad2.undoSignature();
    },
    save() {
      const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
      console.log(isEmpty);
      console.log(data);
    },
    getProfile(){
      this.profile.loading = true
      let url = `${baseUrl}/m_view_profile`
      let payloadData = {
        cif_no : this.user.cif_no,
        token : this.user.token,
      }
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      axios
      .post(url,payload)
      .then((res)=>{
        this.profile.loading = false
        this.profile = res.data
      })
      .catch((res)=>{
        this.profile.loading = false
        this.notif('Error',res.message,'danger')
      })
    },
    doPengajuan(){
      let url = `${baseUrl}/m_submission`
      let payloadData = this.pengajuan.data
      let payload = new FormData()
      for(let key in payloadData){
        payload.append(key,payloadData[key])
      }
      this.pengajuan.loading = true
      axios
      .post(url,payload)
      .then((res)=>{
        this.pengajuan.loading = false
        let vm = this
        if(res.data.status == 1){
          this.notif('Success',res.data.message,'success')
        } else {
          this.notif('Warning',res.data.message,'warning')
        }
        setTimeout(() => {
          vm.$router.push('/pengajuan')
        }, 2000);
      })
      .catch((res)=>{
        this.pengajuan.loading = false
        this.notif('Error',res.message,'danger')
      })
    },
    setJangkawaktu(){
      if(this.pengajuan.data.periode_jangka_waktu == 3){
        this.pengajuan.data.jangka_waktu = 1
      } else {
        this.pengajuan.data.jangka_waktu = 0
      }
    },
    move(step){
      var dateObj = new Date();
      dateObj.setDate(dateObj.getDate() + 3)
      var month = String(dateObj.getUTCMonth() + 1).padStart(2, '0')
      var day = String(dateObj.getUTCDate()).padStart(2, '0')
      var year = dateObj.getUTCFullYear();
      var hours = dateObj.getHours()
      var minutes = dateObj.getMinutes() 
      var seconds = dateObj.getSeconds()
      let lanjut = true
      if(step > 0 && step <= this.pengajuan.maxstep){
        if(Number(this.pengajuan.data.jangka_waktu) < 1){
          this.notif('Warning','Jangka waktu harus diisi','warning')
          lanjut = false
        } else {
          lanjut = true
        }
        if(lanjut){
          if(this.pengajuan.data.sumber_pengembalian == 'gaji'){
            if(step == 2){
              this.pengajuan.step = 4
            }
            else if(step == 3){
              this.pengajuan.step = 1
            }
            else {
              this.pengajuan.step = step
            }
          } else {
            this.pengajuan.step = step
          }
        }
        this.scrollToTop()
      }
      let hpp = Number(this.pengajuan.data.belanja_pembelian)+Number(this.pengajuan.data.persediaan_awal)-Number(this.pengajuan.data.persediaan_akhir)
      let laba_kotor = Number(this.pengajuan.data.omset)-hpp
      let total_biaya_usaha = Number(this.pengajuan.data.piutang)+Number(this.pengajuan.data.biaya_usaha)+Number(this.pengajuan.data.sewa_tempat)
      let keuntungan_usaha = laba_kotor-total_biaya_usaha
      let total_pendapatan = Number(this.pengajuan.data.pendapatan_gaji)+Number(this.pengajuan.data.pendapatan_usaha)+Number(this.pengajuan.data.pendapatan_lainnya)
      let total_biaya = Number(this.pengajuan.data.biaya_rumah_tangga)+Number(this.pengajuan.data.biaya_rekening)+Number(this.pengajuan.data.biaya_kontrakan)+Number(this.pengajuan.data.biaya_pendidikan)+Number(this.pengajuan.data.hutang_lainnya)
      let saving_power = total_pendapatan - total_biaya
      let repayment_capacity = saving_power * 75 / 100
      this.pengajuan.data.hpp = hpp
      this.pengajuan.data.laba_kotor = laba_kotor
      this.pengajuan.data.total_biaya_usaha = total_biaya_usaha
      this.pengajuan.data.keuntungan_usaha = keuntungan_usaha
      this.pengajuan.data.total_pendapatan = total_pendapatan
      this.pengajuan.data.total_biaya = total_biaya
      this.pengajuan.data.saving_power = saving_power
      this.pengajuan.data.repayment_capacity = repayment_capacity
      this.pengajuan.data.cif_no = this.profile.cif_no
      this.pengajuan.data.created_by = this.profile.cif_no
      this.pengajuan.data.token = this.user.token
      this.pengajuan.data.rencana_droping = `${day}/${month}/${year}`
      
      if(step > 5){
        const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
        const sig2 = this.$refs.signaturePad2.saveSignature()
        const isEmpty2 = sig2.isEmpty 
        const data2 = sig2.data 
        if(!isEmpty)
          this.pengajuan.data.ttd_anggota = data
        if(!isEmpty2)
          this.pengajuan.data.ttd_pasangan = data2
        this.doPengajuan()
      }
    },
    scrollToTop(){
      window.scrollTo(0,0)
    },
    notif(title,msg,type){
      this.$bvToast.toast(msg, {
        title: title,
        autoHideDelay: 5000,
        variant: type,
        toaster: 'b-toaster-bottom-center'
      })
    },
    generateDate(){
      var dateObj = new Date();
      var month = String(dateObj.getUTCMonth() + 1).padStart(2, '0')
      var day = String(dateObj.getUTCDate()).padStart(2, '0')
      var year = dateObj.getUTCFullYear();
      var hours = dateObj.getHours()
      var minutes = dateObj.getMinutes() 
      var seconds = dateObj.getSeconds()
      this.pengajuan.data.tanggal_pengajuan = `${day}/${month}/${year}`
      this.pengajuan.data.map_no = `${this.user.cif_no}${year}${month}${day}${hours}${minutes}${seconds}`
    },
    hitungPengajuan(){
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
        this.pengajuan.data.pyd = res.data.length + 1
        this.pengajuan.data.pembiayaan_ke = res.data.length + 1
      })
      .catch((res)=>{
        this.pengajuan.loading = false
        this.notif('Error',res.message,'danger')
      })
    },
    previewImage(event, target) {
      let theImg = null
      let vm = this
      const ktp = this.$refs['fm-ktp']
      const kk = this.$refs['fm-kk']
      const doc = this.$refs['fm-doc']
      let reader = new FileReader();
      switch (target) {
        case 'ktp':
          theImg = event.target.files[0];
          reader.readAsDataURL(theImg);
          reader.onload = function () {
            vm.pengajuan.data.doc_ktp = reader.result
            ktp.type = 'text';
            ktp.type = 'file';
          };
          reader.onerror = function () {
            vm.pengajuan.data.doc_ktp = null
            ktp.type = 'text';
            ktp.type = 'file';
          };
          break;
        case 'kk':
          theImg = event.target.files[0];
          reader.readAsDataURL(theImg);
          reader.onload = function () {
            vm.pengajuan.data.doc_kk = reader.result
            kk.type = 'text';
            kk.type = 'file';
          };
          reader.onerror = function () {
            vm.pengajuan.data.doc_kk = null
            kk.type = 'text';
            kk.type = 'file';
          };
          break;
        case 'doc':
          theImg = event.target.files[0];
          reader.readAsDataURL(theImg);
          reader.onload = function () {
            vm.pengajuan.data.doc_pendukung = reader.result
            doc.type = 'text';
            doc.type = 'file';
          };
          reader.onerror = function () {
            vm.pengajuan.data.doc_pendukung = null
            doc.type = 'text';
            doc.type = 'file';
          };
          break;
      }
    },
    deleteImage(idx) {
      let id = this.form.data.child.image[idx].id
      if (id > 0) {
        this.form.data.child.image[idx].deleted = true
      } else {
        this.form.data.child.image.splice(idx, 1);
      }
    },
  },
  mounted(){
    this.getProfile()
    this.generateDate()
    this.hitungPengajuan()
  }
}
</script>