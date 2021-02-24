<template>
<div :class="wrapperClasses" class="onComponentLoad component-page">
    <form @submit.prevent="saveForm(false)">
        <div class="panel-title-wrap d-flex">
            <div class="flex-grow-1">
                {{ addEditWord }} {{ pageTitle }}
            </div>
            <div class="panel-btns">
                <btn type="submit" variant="outline-success"><i class="fal fa-save"></i><span class="d-none d-sm-inline"> Save</span></btn>
                <btn variant="outline-success" @click="saveForm(true)"><i class="fal fa-save"></i><span class="d-none d-sm-inline"> Save &amp; Close</span></btn>
                <btn variant="outline-primary" @click="closePanel"><i class="fal fa-times"></i><span class="d-none d-sm-inline"> Close</span></btn>
            </div>
        </div>
        
        <div class="form-content">
            <div v-show="loading" class="form-loader"><div><i class="fad fa-circle-notch fa-spin fa-4x"></i></div></div>
            <b-container v-if='formReady'>
                <b-row>
                    <b-col cols="5" class="pr-4">
                        <tree-el @permission-changed="permissionChanged" v-for="(item,key) in pageData.tree" :key="item.id" :tree_item_data="item" :permissions_data="pageData.rolePermissions" />
                    </b-col>
                    <b-col cols="3">
                        <f-set v-model="forms.main.values.name" :data="forms.main.fields.name" />
                    </b-col>
                </b-row>
            </b-container>
        </div>
    </form>
</div>    
</template>

<script>

import form from '@/mixins/form'
import panel from '@/mixins/panel'
import treeEl from './PermissionTreeEl'

export default {
    mixins: [form,panel],
    components: {
        'tree-el': treeEl
    },

    data() {
        return {
            pageTitle: 'User Permission',
            formUri: '/user-roles',
            formId: 'usersRolesForm',
            dataWithSaveRequest: [] //setting it as an array default to allow findIndex
        }
    },
    methods: {
        permissionChanged(val) {
            
            var index = this.dataWithSaveRequest.findIndex((item) => item.sections_id == val.sections_id)

            if (index != -1) {

                this.dataWithSaveRequest[index].permission = val.value;

            } else {
                
                this.dataWithSaveRequest.push({ 
                    sections_id: val.sections_id, 
                    permission: val.value 
                })

            }

        }
    },
}
</script>