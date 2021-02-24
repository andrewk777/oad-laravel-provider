<template>
    <div class="onComponentLoad">
       
    </div>
</template>

<script>

import store from "@/store";
import formatting from '@/mixins/formatting';

export default {
    mixins: [ formatting ],
    data() {
        return {
        }
    },
    mounted() {
    }
}
</script>

<style scoped>
.chart-title {
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    padding-bottom: 15px;
}
</style>