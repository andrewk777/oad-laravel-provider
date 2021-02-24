<template>
    <div class="d-flex wrap">
        <router-view/>
    </div>
</template>

<script>
export default {
    created() {
        document.body.classList.add("front");
    }
}
</script>

<style scoped>
.wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
}
</style>