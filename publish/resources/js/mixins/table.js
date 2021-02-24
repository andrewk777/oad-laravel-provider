import { mapGetters } from 'vuex';
import _ from 'underscore';

const mixin = {
    data() {
        return {
            loading: true,
            filterOnChange: true
        }
    },
    computed: {
        mainTable() {
            return this.vTablesConfigs[this.mainTableRef];
        }
    },
    methods: {
        tableRefresh(tableRef) {
            this.loading = true;
            this.$events.fire('vt-refresh-' + tableRef, {})
        },
        makeTable(obj) {
            var params = {
                    url: '',
                    ref: '',
                    formComponentName: '',
                    showFilters: false,
                    checkboxes: [],
                    onRowClass: false,
                    filters: {},
                    cols: [],
                    perPage: 25,
                    resData: [],
                    resExtraData: {},
                    sortOrder: [{ field: 'name', direction: 'asc' }],
            }
            obj.id = obj.id ?? obj.name
            _.extend(params, obj)
            
            if ( typeof(this.$store.getters.getTables[params.ref]) == 'undefined' ) { //create an table instance in vuex

                this.$store.commit('setNewTable', params.ref);

            } else if (this.$store.getters.getTables[params.ref].colsToggled.length > 0) { //set column visibility state && filters from vuex
                var savedColsToggled = this.$store.getters.getTables[params.ref].colsToggled;
                var savedCol = false;

                params.cols.forEach((col,indx) => {
                    if (savedCol = savedColsToggled.find(o => o.name == col.name)) {
                        params.cols[indx].visible = savedCol.visible;
                    }
                })

                params.filters = this.$store.getters.getTables[params.ref].filters;

            }
            return params;
        },
        tableClearFilters(tableRef) {
            var table = this.vTablesConfigs[tableRef];
            table.filters = typeof table.dFilters != 'undefined' ? table.dFilters : {};
            table.showFilters = false;
            this.refreshTable(tableRef);
        },
        showHideFilters(tableRef) {
            var table = this.vTablesConfigs[tableRef];
            table.showFilters = !table.showFilters;
        },
        checkboxToggled(data) {
            this.vTablesConfigs[data.tableRef].checkboxes = data.checkboxes
        },
        toggleColumn(tableRef,colName) {
            var tableConf = this.vTablesConfigs[tableRef]
            var colIndx = tableConf.cols.findIndex( o => o.name == colName);
            var col = tableConf.cols[colIndx]
            col.visible = typeof col.visible == 'undefined' ? false : !col.visible 
            this.$store.commit('setTableColToggled', { tableRef: tableRef, col: { name: colName, visible: col.visible  } });
            this.$refs[tableRef].$refs[tableRef].normalizeFields()
        },
        togglableCols(tableRef) {

            return this.vTablesConfigs[tableRef].cols.filter( o => o.canToggle )

        },
        tableAeItem(tableRef, data) {
            
            var tableConf   = this.vTablesConfigs[tableRef];
            var index       = typeof data !== 'undefined' ? data.index : 0;
            var action      = typeof data !== 'undefined' ? data.action : 'add';
            var rowData     = typeof data !== 'undefined' ? data.rowData : {};
            var hash        = typeof rowData.hash !== 'undefined' ? rowData.hash: 0;

            this['tableAction_' + action]({
                parentType: 'table',
                action:     action,
                data:       rowData,
                index:      index,
                tableConf:  tableConf,
                hash:       hash
            })
        },

        tableAction_add(data) {
            this.tableAction_edit(data);
        },
        tableAction_edit(data) {

            var formID = data.tableConf.formComponentName;

            data.parent = {
                type:   'table',
                ref:    data.tableConf.ref
            }

            this.openForm(formID, data, { hash: data.hash });

        },

        tableAction_delete(data) {

            this.$utils.swalConfigm.fire({
                title: 'Delete this item?',
                text: '',
                showCancelButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.value) {
                    axios.delete(data.tableConf.url + '/' + data.data.hash)
                    .then((res) => {

                        this.$utils.c_notify(res.data.res, res.data.status);
                        if (res.data.status == 'success') {
                            this.tableRefresh(data.tableConf.ref)
                        }

                    })
                }
            })

        },
        tableExport(tableConf) {
            var table = this.vTablesConfigs[tableConf];
            var url = typeof table.exportUrl !== 'undefined' ? table.exportUrl : table.url + '/export';
            axios({
                url: url,
                method: 'post',
                data: table.filters
            })
            .then((res) => {
                window.open('/download-tmp-file/' + res.data, '_blank')                  
                this.showLoader = false;
            })
        },
        onDataLoaded(tableRef) {
            var resData = this.mainTable.resData;
            this.$refs[tableRef].$refs[tableRef].selectedTo = [];
            this.checkboxToggled({ tableRef: this.mainTableRef, checkboxes: [] })
            this.loading = false;
        },
        openForm(formID,data,urlParams) {

            urlParams = typeof urlParams !== 'undefined' ? urlParams : {};
            var parent      = typeof data.parent !== 'undefined' ? data.parent : { type: '', ref: ''};
            var action      = typeof data.action !== 'undefined' ? data.action : 'add';
            var cdata     = typeof data.data !== 'undefined' ? data.data : {};
            var index       = typeof data.index !== 'undefined' ? data.index : 0;
            var hash        = typeof data.hash !== 'undefined' ? data.hash: 0;

            this.$store.commit('setFormParams',{
                id: formID,
                data: {
                    parent:     parent,
                    action:     action,
                    data:       cdata,
                    index:      index,
                    hash:       hash
                }
            })

            this.$router.push({ name: formID, params: urlParams })

        }

    },
    mounted() {
        //assign watchers to table filters to update table on filter change
        for (const key in this.vTablesConfigs) {
            this.$watch('vTablesConfigs.' + key + '.filters', function () {

                this.$store.commit('setTableFilters', {
                    ref: this.vTablesConfigs[key].ref,
                    val: this.vTablesConfigs[key].filters
                });
                if (this.filterOnChange) { //filter when filter value changes
                    this.tableRefresh(this.vTablesConfigs[key].ref);
                }

            }, {deep: true})
            
            this.$watch('vTablesConfigs.' + key + '.resData', function () {

                this.onDataLoaded(key);

            }, {deep: true})

          }
    }
}
export default mixin
