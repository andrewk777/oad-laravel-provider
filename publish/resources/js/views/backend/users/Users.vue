<template>
<div :class="pageClass" class="onComponentLoad">
    
    <div class="panel-title-wrap">{{ pageTitle }}</div>

    <b-container>
        <b-row class="mb-3 d-flex justify-content-between">
            <div>
                <table-search :tableRef="mainTableRef"></table-search>
            </div>

            <div>
                <b-button variant="outline-primary" @click="tableAeItem(mainTableRef)"><i class="fal fa-plus"></i><span class="d-none d-sm-inline"> Add</span></b-button>
                <b-button variant="outline-primary" @click="showHideInactive"><i class="fal fa-user"></i><span class="d-none d-sm-inline"> {{ btnText }} </span></b-button>
                <b-button variant="outline-primary" @click="tableRefresh(mainTableRef)"><i class="fal fa-redo"></i><span class="d-none d-sm-inline"> Refresh</span></b-button>
            </div>
        </b-row>
    </b-container>

    <my-vuetable :ref="mainTableRef" @action-btn-clicked="tableAeItem" :tableConf="mainTable" />

    <router-view/>

</div>
</template>

<script>
import table from '@/mixins/table';
import formatting from '@/mixins/formatting';
import { mapGetters } from 'vuex';

export default {
    mixins: [ table, formatting ],
    data() {
        return {
            pageTitle: 'Users',
            pageClass: 'users-index',
            mainTableRef: 'usersIndex',
            vTablesConfigs: {
                usersIndex: this.makeTable({
                    url: '/users',
                    ref: 'usersIndex',
                    formComponentName: 'usersForm',
                    filters: {
                        sys_access: 1
                    },
                    cols: [
                        { name: 'hash', visible: false, searchable: false },
                        { name: 'name', title: 'Full Name', sortField: 'name' },
                        { name: 'email', title: 'Email', sortField: 'email', titleClass: 'w-300-px' },
                        { name: 'role', db: 'users_roles.name', title: 'Access Level', titleClass: 'text-center w-200-px', dataClass: 'text-center',sortField: 'role' },
                        { name: 'sys_access', sortField: 'sys_access', title: 'Access', titleClass: 'text-center', dataClass: 'text-center w-150-px', callback: this.YesNo, searchable: false },
                        { name: '__component:table-action-btns', title: '', titleClass: 'dt-action-col', dataClass: 'p-0', noSelect: true, searchable: false }
                    ]
                })
            }
        }
    },
    computed: {
        btnText() {
            return this.vTablesConfigs[this.mainTableRef].filters.sys_access ? 'Show All Users' : 'Show Active Users Only';
        }
    },
    methods: {
        showHideInactive() {
            var table = this.vTablesConfigs[this.mainTableRef];
            table.filters.sys_access = table.filters.sys_access ? 0 : 1;            
        }
    }
}
</script>

<style>
</style>
