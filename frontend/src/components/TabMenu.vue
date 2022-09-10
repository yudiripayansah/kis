<template>
  <div class="pg-tabmenu">
    <router-link to="/" :class="($route.name == 'Dashboard') ? 'active' : null">
      <i class="fas fa-home"></i>
    </router-link>
    <router-link to="/saldo-anggota" :class="($route.name == 'SaldoAnggota') ? 'active' : null">
      <b-icon icon="person"/>
    </router-link>
    <router-link to="/transaksi" :class="($route.name == 'Transaksi' || $route.name == 'TransaksiKirim' || $route.name == 'TransaksiTerima' || $route.name == 'TransaksiHistori') ? 'active' : null">
      <b-icon icon="hand-index"/>
    </router-link>
    <router-link to="/saldo-pembiayaan" :class="($route.name == 'SaldoPembiayaan') ? 'active' : null">
      <b-icon icon="credit-card2-back-fill"/>
    </router-link>
    <router-link to="/saldo-tabungan" :class="($route.name == 'SaldoTabungan') ? 'active' : null">
      <i class="fas fa-money-bill-wave"></i>
    </router-link>
    <router-link to="/pengajuan" :class="($route.name == 'Pengajuan' || $route.name == 'PengajuanForm') ? 'active' : null" v-show="user.cif_type == 1">
      <i class="fas fa-clipboard-check"></i>
    </router-link>
  </div>
</template>
<script>
import {
  mapGetters
} from "vuex";
export default {
  name : 'TabMenu',
  watch : {
    '$route'(val){
      console.log(val.name)
    }
  },
  computed: {
    ...mapGetters(["user"])
  },
}
</script>