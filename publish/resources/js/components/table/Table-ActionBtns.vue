<template>
<div class="dt-action-btns">

    <b-dropdown v-if="dropdownBtns.length >= 1" variant="outline-primary" right split :text="mainActionBtn.text"  @click="itemAction(mainActionBtn.action, rowData, rowIndex)" >
        <b-dropdown-item v-for="item in dropdownBtns"
            :key="item.action"
            class="w-100"
            @click="itemAction(item.action, rowData, rowIndex)">
            {{ item.text }}
        </b-dropdown-item>
    </b-dropdown>
    <b-button v-else variant="outline-primary" class="btn-sm w-100" @click="itemAction(mainActionBtn.action, rowData, rowIndex)">{{ mainActionBtn.text }} </b-button>

</div>
</template>

<script>

export default {
    props: {
        rowData: {
            type: Object,
            required: true
        },
        rowIndex: {
            type: Number
        }
    },
    computed: {
        mainActionBtn() {
            return this.rowData.actions.length ? this.rowData.actions[0] : {};
        },
        dropdownBtns() {
            var items = [...this.rowData.actions];
            
            if (items.length > 1 ) {
                items.splice(0,1)                
            } else {
                items = [];
            }
            return items;
        }
    },
    methods: {
        itemAction(action, data, index) {
            this.$parent.$emit('btn-clicked',{
                action:action,
                data:data,
                index:index
            });
        }
    }
}
</script>

<style>
.dt-action-btns button.ui.button {
    padding: 8px 8px;
}

.dt-action-btns button.ui.button > i.icon {
    margin: auto !important;
}
</style>
