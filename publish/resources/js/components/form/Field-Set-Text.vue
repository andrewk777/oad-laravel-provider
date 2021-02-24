<template>
    <fieldset class="fieldset">
        <legend>{{ label }}:</legend>
        <div class="clearfix"></div>
        <span class="field-text" v-html="text"></span>
    </fieldset>
</template>

<script>

export default {
	props: ['label','text'],
}
</script>
