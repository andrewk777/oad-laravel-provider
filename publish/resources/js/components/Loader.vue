<template>
    <div class="vld-parent">
        <loading
            :active.sync="isLoading"
            :is-full-page="fullPage"
            :color="color"
            >
        </loading>
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import { CONF } from '@/config';

    export default {
        data() {
            return {
                isLoading: true,
                fullPage: false,
                color: CONF.PRIMARY_COLOR
            }
        },
        components: {
            Loading
        },
        methods: {
            doAjax() {
                this.isLoading = true;
                // simulate AJAX
                setTimeout(() => {
                  this.isLoading = false
                },5000)
            },
            onCancel() {
              console.log('User cancelled the loader.')
            }
        }
    }
</script>
