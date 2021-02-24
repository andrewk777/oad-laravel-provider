<template>
<div>
    <div class="menu">
        <img src="/images/logo-icon-ww.png" class="logo pb-4 pt-3">
        <ul>
            <li v-for="(item,key) in menu_primary" :key="item.id" @mouseover="sh_secondary_menu(item.id)" :id="get_menu_id(item.id)">
                <router-link :to="item.routes.path" :event="typeof menu_secondary[item.id] == 'undefined' ? 'click' : ''">
                    <i :class="item.cssClass"></i>
                    {{ item.text }}
                </router-link>
            </li>            
        </ul>
    </div>

    <div 
        
        :class="{ opened: $parent.second_menu_opened, 'dontshow': init }" 
        class="secondary-menu" >
        <ul ref="second_menu_list">
            <li v-for="(item,key) in second_menu_items" :key="key" @click="$parent.second_menu_opened = false">
                <router-link :to="item.routes.path"> {{ item.text }}</router-link>
            </li>
        </ul>
    </div>

</div>
</template>

<script>

export default {
    props: ['menu_primary','menu_secondary'],
    data() {
        return {
            init: true,
            second_menu_items: {}
        }
    },
    methods: {
        get_menu_id(id){
            return 'main_menu_' + id;
        },
        adjust_second_menu_position(id) {
            var element = document.getElementById('main_menu_' + id)
            var rect = element.getBoundingClientRect();
            this.$refs.second_menu_list.style.marginTop = Math.round(rect.top) + 'px'
        },
        sh_secondary_menu(parent_id) {

            this.adjust_second_menu_position(parent_id)

            this.$parent.second_menu_opened = typeof this.menu_secondary[parent_id] !== 'undefined'
            if (this.$parent.second_menu_opened) {
                this.second_menu_items = this.menu_secondary[parent_id];
            }
        }
    },
    mounted() {
        setTimeout(() => { this.init = false; }, 310)
    }
}
</script>

<style scoped>
/* fixing secondary menu showing on load */
.dontshow {
    visibility: hidden
}
</style>