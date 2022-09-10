<template>
  <div class="w-100 d-flex">
    <router-view></router-view>
    <!-- <transition name="slide-fade">
    <TabMenu v-show="$route.name != 'Dashboard'"/>
    </transition> -->
  </div>
</template>
<script>
import TabMenu from '../components/TabMenu'
export default {
  components: {
    TabMenu
  },
  data(){
    return {
      transitionName : null
    }
  },
  watch: {
    
  }
}
</script>
<style lang="scss">
/* Enter and leave animations can use different */
/* durations and timing functions.              */
.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateY(100px);
  opacity: 0;
}
</style>
