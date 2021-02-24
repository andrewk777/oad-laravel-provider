<template>
  <div>    

    <top-bar></top-bar>

    <spa-menu :menu_primary="menu_primary" :menu_secondary="menu_secondary" />

    <div class="content-wrap">
        <div class="content" @mouseover="second_menu_opened = false">

          <router-view></router-view>

        </div>

        <spa-footer></spa-footer>
    </div>
  </div>
</template>


<script>

import menu from "./menu";
import topBar from "./topBar";
import footer from "./footer";
import { confirmLogout } from "@/auth";

export default {
    components: {
        'top-bar': topBar,
        'spa-menu': menu,
        'spa-footer': footer
    },
    data() {
        return {
            menu_primary: {},
            menu_secondary: {},
            second_menu_opened: false,
        }
    },
    methods: {
        confirmLogout
    },
    mounted() {
      document.body.classList.remove("front");
      axios.get('/layout')
      .then(res => {
          if (typeof res !== 'undefined') {
              this.menu_primary = res.data.menu_primary
              this.menu_secondary = res.data.menu_secondary
          }          
      })
    }
};
</script>