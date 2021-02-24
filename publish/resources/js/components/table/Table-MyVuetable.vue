<template>
<div>
    <div class="table-wrap">
        <div v-if="VTLoading" class="table-loader"><div><i class="fad fa-circle-notch fa-spin fa-4x"></i></div></div>
        <vuetable-pagination v-if="showPgTop" ref="paginationTop" @vuetable-pagination:change-page="onChangePage"></vuetable-pagination>
        <vuetable :ref="tableRef"
            :api-url="apiUrl"
            :css="css.table"
            :fields="tableColumns"
            :sort-order="sortOrder"
            :per-page="perPage"
            :http-options="httpOptions"
            http-method="post"
            @btn-clicked="btnClicked"
            pagination-path="pagination"
            data-path="data"
            @vuetable:pagination-data="onPaginationData"
            @vuetable-pagination:change-page="onChangePage"
            :append-params="dtParams"
            :http-fetch="fetchData"
            :row-class="onRowClass"
            @vuetable:loading="VTLoading = true"
            @vuetable:loaded="VTLoading = false"
            @vuetable:checkbox-toggled="checkboxToggled"
            @vuetable:checkbox-toggled-all="checkboxToggled"
            :transform="transform"
            >
        </vuetable>
    </div>
    <div v-if="showPgBot" class="vuetable-pagination ui basic segment grid d-flex justify-content-between">
      <vuetable-pagination-info ref="paginationInfo"></vuetable-pagination-info>
      <vuetable-pagination :css="css.pagination" ref="paginationBot" @vuetable-pagination:change-page="onChangePage"></vuetable-pagination>
    </div>
</div>
</template>

<script>
// import accounting from 'accounting'
import Vuetable from 'vuetable-2/src/components/Vuetable'
import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
import Vue from 'vue'
import VueEvents from 'vue-events'
import { CONF } from '@/config';
import { utils } from '@/utils';
import { mapGetters } from 'vuex';
import _ from 'underscore';
import store from "@/store";
Vue.use(VueEvents)

export default {
    components: {
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo
    },
    props: ['tableConf'],
    computed: {
        tableColumns() {
            return this.tableConf.cols;
        },
        sortOrder() {
            return typeof this.tableConf.sortOrder !== 'undefined' ? this.tableConf.sortOrder : '';
        },
        perPage() {
            return typeof this.tableConf.perPage !== 'undefined' ? this.tableConf.perPage : 25;
        },
        showPgTop() {
            return typeof this.tableConf.showPgTop !== 'undefined' ? this.tableConf.showPgTop : false;
        },
        showPgBot() {
            return typeof this.tableConf.showPgBot !== 'undefined' ? this.tableConf.showPgBot : true;
        },
        apiUrl() {
            return typeof this.tableConf.url !== 'undefined' ? CONF.API_URL + this.tableConf.url : '';
        },
        tableRef() {
            return typeof this.tableConf.ref !== 'undefined' ? this.tableConf.ref : 'indx';
        },
        dtParams() {

            //mergin user selected filters with system filters
            var filters = typeof(this.tableConf.filters) !== 'undefined' ? this.tableConf.filters : {};
            var search = '';
            //applying stored filters and search
            if (typeof(this.$store.getters.getTables[this.tableRef]) != 'undefined' ) {
                _.extend(filters, this.$store.getters.getTables[this.tableRef].filters)
                search = this.$store.getters.getTables[this.tableRef].search;
            }

            return {
                search: search,
                config: this.tableConf,
                filters: filters
            }
            
        },
        httpOptions() {
            return {
                headers: {
                    'Accept': 'application/json, text/plain, */*',
                    'Authorization': 'Bearer ' + store.state.accessToken
                }
            }
        }
    },
    data() {
        return {
            VTLoading: true,
            css: {
                table: {
                    tableClass:     'ui blue selectable celled stackable attached table',
                    loadingClass:   'loading',
                    ascendingIcon: 'far fa-chevron-up pt-1',
                    descendingIcon: 'far fa-chevron-down pt-1',
                    detailRowClass: 'vuetable-detail-row',
                    handleIcon:     'grey sidebar icon',
                    sortableIcon:   '',  // since v1.7
                    ascendingClass: 'sorted-asc', // since v1.7
                    descendingClass: 'sorted-desc' // since v1.7
                },
                pagination: {
                    wrapperClass: 'v-pagination card d-flex flex-row',
                    activeClass: 'active',
                    disabledClass: 'disabled',
                    pageClass: 'item',
                    linkClass: 'icon item',
                    paginationClass: ' pt-3',
                    paginationInfoClass: ' pt-4',
                    dropdownClass: 'ui search dropdown',
                    icons: {
                        first: 'fal fa-angle-double-left',
                        prev: 'far fa-angle-left',
                        next: 'far fa-angle-right',
                        last: 'fal fa-angle-double-right',
                    }
                },
                paginationInfo: {
                    infoClass: 'pt-3',
                }
            }
        }
    },
    methods: {
        fetchData() {
            var promise = axios.post(this.apiUrl, this.httpOptions);
            var table = this.$parent.vTablesConfigs[this.tableRef];
            promise.then((res) => { 
                table.resData = res.data.data; 
                table.resExtraData = res.data.extraData;
            })
            return promise
        },
        transform(tableData) {

            var timestamp = Date.now()

            for (var i = 0; i < tableData.data.length; i++) {
                tableData.data[i].timestamp = timestamp;
            }
            return tableData;
        },
        checkboxToggled() {
            this.$emit('checkbox-toggled', {
                tableRef: this.tableConf.ref,
                checkboxes: this.$refs[this.tableConf.ref].selectedTo
            })
        },
        onPaginationData (paginationData) {
            if (this.showPgTop)
                this.$refs.paginationTop.setPaginationData(paginationData)
            if (this.showPgBot) {
                this.$refs.paginationBot.setPaginationData(paginationData)
                this.$refs.paginationInfo.setPaginationData(paginationData)
            }
        },
        onChangePage(page) {
            this.$store.commit('setTablePage', this.tableRef, page);
            this.$refs[this.tableRef].changePage(page)
        },
        btnClicked(data) {            
            this.$emit('action-btn-clicked',this.tableRef,{
                action:     data.action,
                rowData:    data.data
            });
        },
        onVTRefresh() {
            Vue.nextTick( () => { this.$refs[this.tableRef].refresh(); } )
        },
        onRowClass(dataItem, index) {

            if (typeof(this.tableConf.onRowClass) === 'function') {
                return this.tableConf.onRowClass(dataItem,index)
            }
            return '';
        }
    },
    created() {
        this.$events.$on('vt-refresh-' + this.tableRef, e => this.onVTRefresh(e))
    },
    destroyed() {
        this.$events.remove('vt-refresh-' + this.tableRef)
    }

}
</script>