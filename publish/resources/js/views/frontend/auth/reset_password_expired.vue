<template>
    <div class="login-box p-5 onComponentLoadFront">
        <div class="text-center pb-5">
            <img src="/images/logo.png">
        </div>
        <p>The Link Has Expired, <br>please resend the request</p>

        <div class="text-center">
            <router-link :to="{ name: 'forgot_password' }" class="text-secondary">Click Here to Reset Password</router-link>
        </div>

    </div>
</template>

<script>
export default {
}
</script>

<style scoped>
p {
    text-align: center;
    font-size: 18px;
    padding-bottom: 20px;
}
</style>