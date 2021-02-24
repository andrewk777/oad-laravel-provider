<template>
    <div class="login-box p-5 onComponentLoadFront">
        <div class="text-center pb-5">
            <img src="/images/logo.png">
        </div>
        <form @submit.prevent="save">

            <div class="input-group mb-3">
                <input v-model="form.password" type="password" class="form-control" placeholder="New Password">
                <div class="input-group-append"><span class="input-group-text"><i class="far fa-lock-alt"></i></span></div>
            </div> 
            

            <div class="input-group mb-5">
                <input v-model="form.password_confirm" type="password" class="form-control" placeholder="Confirm Password">
                <div class="input-group-append"><span class="input-group-text"><i class="far fa-lock-alt"></i></span></div>
                <small>
                    <ul>
                        <li>Must be minimum 6 characters</li>
                        <li>Must contain 1 Lower Case Letter</li>
                        <li>Must contain 1 Upper Case Letter</li>
                        <li>Must contain 1 Digit</li>
                        <!-- <li>Must contain 1 special character (@$!%_*#?&.)</li> -->
                    </ul>
                </small>
            </div> 

            <btn type="submit" css="w-100 btn-primary mb-3">Save New Password</btn>

            <div class="text-center">
                <router-link :to="{ name: 'login' }" class="text-secondary">Back to Login</router-link>
            </div>

        </form>

    </div>
</template>

<script>

import { resetPassword } from "@/auth";
import form from '@/mixins/form'

export default {
    mixins: [form],
    data() {
        return {
            form: {
                email: '',
                token: '',
                password: '',
                password_confirm: ''
            },
        }
    },
    methods: {
        save() {
            resetPassword(this)
        }
    },
    mounted() {
        this.form.email = this.$route.params.email;
        this.form.token = this.$route.params.token;
    }
}
</script>

<style scoped>
small {
    flex-basis: 100%;
    padding: 10px 0px 0px 10px;
    font-style: italic;
}
</style>