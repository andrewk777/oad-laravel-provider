<template>
    <div class="pl-4 pt-3">
        <div class="d-flex">
            <div style="flex-grow:1">{{ tree_item_data.text }}</div>
            <div class="w-150-px">
                <f-set v-model="pValue" @input="setValue" :data="fieldData"/>
            </div>
        </div>
        <tree-el @permission-changed="permissionChanged" v-for="(item,key) in tree_item_data.children" :key="item.id" :tree_item_data="item" :permissions_data="permissions_data" />
    </div>
</template>

<script>

import fieldBuilder from '@/mixins/fieldBuilder';

export default {
    mixins: [ fieldBuilder ],
    name: 'tree-el',
    props: ['tree_item_data','permissions_data'],
    data() {
        return {
            fieldData: this.makeField({ name: 'select-option', type: 'select', options: this.tree_item_data.access_options, allow_clear: false }),
            pValue: ''
        }
    },
    methods: {
        setValue(val) {
            this.$emit('permission-changed', { sections_id: this.tree_item_data.id, value: val } )
        },
        permissionChanged(val) {
            this.$emit('permission-changed', val )
        }
    },
    mounted() {
        var saved_permission = this.permissions_data.filter((permission) => permission.sections_id == this.tree_item_data.id).map((obj) => obj.permission)

        this.pValue = saved_permission.length ? 
                        saved_permission[0] : 
                        this.tree_item_data.access_options.filter((permission) => permission.code == 'view' || permission.code == 'full').map((obj) => obj.code)[0];
        
        this.setValue(this.pValue)
    }
}
</script>