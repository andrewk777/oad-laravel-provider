<template>
    <div class="login-box p-5 onComponentLoadFront">
        <div class="text-center pb-5">
            <img src="/images/logo.png">
        </div>
        <form @submit.prevent="send">

            <div class="input-group mb-5">
                <input v-model="form.email" type="text" class="form-control" placeholder="Email">
                <div class="input-group-append"><span class="input-group-text"><i class="far fa-envelope"></i></span></div>
            </div> 

            <btn type="submit" css="w-100 btn-primary mb-3">Reset Password</btn>

            <div class="text-center">
                <router-link :to="{ name: 'login' }" class="text-secondary">Back to Login</router-link>
            </div>

        </form>

    </div>
</template>

<script>

import { sendPasswordResetEmail } from "@/auth";
import form from '@/mixins/form'

export default {
    mixins: [form],
    data() {
        return {
            form: {
                email: ''
            }
        }
    },
    methods: {
        send() {
            sendPasswordResetEmail(this)
        }
    }
}
</script>