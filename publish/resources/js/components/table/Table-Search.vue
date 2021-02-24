<template>
    <b-input-group class="tableSearchGroup">
        <b-form-input ref="input" class="tableSearch" placeholder="Search" v-model="searchTerm"></b-form-input>
        <b-input-group-append>
            <span class="input-group-text border-left-0"><i class="fal fa-search blue"></i></span>
        </b-input-group-append>
    </b-input-group>
</template>

<script>

export default {
    props: ['tableRef'],
    data() {
        return {
            searchTerm : '',
            delayTimer : {}
        }
    },
    watch:{
        searchTerm: function(val){

            clearTimeout(this.delayTimer);

            this.delayTimer = setTimeout(() => {
                
                if (this.$store.getters.getTables[this.tableRef].search != val) {
                    this.$store.commit('setTableSearchTerm', {ref: this.tableRef , val: val});
                    this.$events.fire('vt-refresh-' + this.tableRef, {})
                }
            } , 300)

        }
    },
    mounted() {
        if (typeof this.$store.getters.getTables[this.tableRef] !== 'undefined') //undefined on the first load
            this.searchTerm = this.$store.getters.getTables[this.tableRef].search;
    }

}
</script>
