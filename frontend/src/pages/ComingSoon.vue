<template>
  <div class="pg-coming-soon">
    <img :src="app.koperasi_logo" alt="" class="pg-logo">
    <!-- <h5>Mobile {{app.koperasi_name}}</h5> -->
    <img src="../assets/images/coming-soon.png" alt="" class="img-fluid">
  </div>
</template>
<script>
import {settings} from '../config'
export default {
  data(){
    return {
      app:settings
    }
  }
}
</script>