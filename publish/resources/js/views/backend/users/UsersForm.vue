<template>
<div :class="wrapperClasses" class="onComponentLoad component-page">
    <form @submit.prevent="saveForm(false)">
        <div class="panel-title-wrap d-flex">
            <div class="flex-grow-1">
                {{ addEditWord }} {{ pageTitle }}
            </div>
            <div class="panel-btns">
                <btn @click="setSelVal" variant="outline-success"><i class="fal fa-save"></i><span class="d-none d-sm-inline"> set val</span></btn>
                <btn type="submit" variant="outline-success"><i class="fal fa-save"></i><span class="d-none d-sm-inline"> Save</span></btn>
                <btn variant="outline-success" @click="saveForm(true)"><i class="fal fa-save"></i><span class="d-none d-sm-inline"> Save &amp; Close</span></btn>
                <btn variant="outline-primary" @click="closePanel"><i class="fal fa-times"></i><span class="d-none d-sm-inline"> Close</span></btn>
            </div>
        </div>
        
        <div class="form-content">
            <div v-show="loading" class="form-loader"><div><i class="fad fa-circle-notch fa-spin fa-4x"></i></div></div>
            <b-container v-if='formReady'>
                <b-row>
                    <b-col>
                        <f-set v-model="forms.main.values.name" :data="forms.main.fields.name"/>
                    </b-col>
                    <b-col>
                        <f-set v-model="forms.main.values.email" :data="forms.main.fields.email"/>
                    </b-col>
                    <b-col>
                        <f-set v-model="forms.main.values.password" :data="forms.main.fields.password"/>
                    </b-col>
                    <b-col>
                        <f-set v-model="forms.main.values.roles_id" :data="forms.main.fields.roles_id"/>
                    </b-col>
                    <b-col>
                        <f-set v-model="forms.main.values.sys_access" :data="forms.main.fields.sys_access"/>
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

export default {
    mixins: [form,panel],
    data() {
        return {
            pageTitle: 'User',
            formUri: '/users',
            formId: 'usersForm'
        }
    },
    methods: {
        setSelVal() {
            this.forms.main.values.roles_id = this.forms.main.values.roles_id == 1 ? 2 : 1
            console.log(this.forms.main.values.roles_id);
        }
    }
}
</script>