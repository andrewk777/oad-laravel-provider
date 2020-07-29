<template>
<div :class="pageClass" class="module-content">
    <b-row class="mb-2">

        <table-search :tableRef="mainTableRef"></table-search>

        <b-col class="text-right">
            <b-button variant="outline-success" @click="aeTableItem(mainTableRef)"><i class="fal fa-plus"></i><span class="d-none d-sm-inline"> Add</span></b-button>
            <b-button variant="outline-warning" @click="vTablesConfigs[mainTableRef].showFilters = !vTablesConfigs[mainTableRef].showFilters"><i class="fal fa-filter"></i><span class="d-none d-sm-inline"> Filters</span></b-button>
            <b-button variant="outline-info" @click="refreshTable(mainTableRef)"><i class="fal fa-redo"></i><span class="d-none d-sm-inline"> Refresh</span></b-button>
        </b-col>
    </b-row>

    <SmoothReflow>
        <table-filters v-show="vTablesConfigs[mainTableRef].showFilters" @clear-filters="clearFilters(mainTableRef)" />
    </SmoothReflow>

    <my-vuetable @action-btn-clicked="aeTableItem" :tableConf="vTablesConfigs[mainTableRef]" />

    <slideout-panel></slideout-panel>

</div>
</template>

<script>

import formAE from './{{ $vue_component_form_name }}'
import table from '@src/mixins/table';
import panel from '@src/mixins/panel';
import formatting from '@src/mixins/formatting';
import indexPage from '@src/mixins/indexPage';

export default {
    components: { formAE },
    mixins: [table,formatting,panel,indexPage],
    data() {
        return {
            pageTitle:      '{{ $page_title_plural }}',
            pageClass:      'indxCss-{{ $vue_component_index_name }}',
            mainTableRef:   'indxTable_{{ $vue_component_index_name }}',
            vTablesConfigs: {
                indxTable_{{ $vue_component_index_name }}: {
                    url: '{{ $base_api_route }}',
                    ref: 'indxTable_{{ $vue_component_form_name }}',
                    panelComponent: formAE,
                    showFilters: false,
                    cols: [
                        { name: 'hash', visible: false, searchable: false },
                        { name: '__component:table-action-btns', title: '', titleClass: 'dt-action-col', noSelect: true, searchable: false }
                    ],
                    sortOrder: [{ field: 'name', direction: 'desc' }],
                    perPage: 15,
                }
            }
        }
    },
}
</script>

<style>
</style>
