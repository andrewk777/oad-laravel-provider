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
            pageTitle: 'User Permissions',
            pageClass: 'users-permissions',
            mainTableRef: 'userPermissionsIndex',
            vTablesConfigs: {
                userPermissionsIndex: this.makeTable({
                    url: '/user-roles',
                    ref: 'userPermissionsIndex',
                    formComponentName: 'usersRolesForm',
                    cols: [
                        { name: 'hash', db: 'users_roles.id', visible: false, searchable: false },
                        { name: 'name', title: 'Role', sortField: 'name' },
                        { name: '__component:table-action-btns', title: '', titleClass: 'dt-action-col', noSelect: true, searchable: false }
                    ]
                })
            }
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
