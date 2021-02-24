<template>
    <div class="login-box p-5 onComponentLoadFront">
        <div class="text-center pb-5">
            <img src="/images/logo.png">
        </div>
        <form @submit.prevent="loginAction">

            <div class="input-group mb-3">
                <input v-model="form.email" type="text" class="form-control" placeholder="Email">
                <div class="input-group-append"><span class="input-group-text"><i class="far fa-envelope"></i></span></div>
            </div>

            <div class="input-group mb-5">
                <input v-model="form.password" type="password" class="form-control" placeholder="Password">
                <div class="input-group-append"><span class="input-group-text"><i class="far fa-lock-alt"></i></span></div>
            </div>

            <btn type="submit" css="w-100 btn-primary mb-3">Login</btn>

            <div class="text-center">
                <router-link :to="{ name: 'forgot_password' }" class="text-secondary">Forgot Password</router-link>
            </div>

        </form>

    </div>
</template>

<script>

import { login } from "@/auth";
import form from '@/mixins/form'

export default {
    mixins: [form],
    data() { 
        return {
            form: {
                email: '',
                password: '',
            }
        } 
    },
    methods: {
        loginAction() {
            login(this)
        }
    }
}
</script>