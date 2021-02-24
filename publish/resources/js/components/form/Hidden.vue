<template>
    <input type="hidden"
        :id="fieldId"
        :name="fieldName"
        :value="value"
        :class="fieldClass"
        @input="sendEmit"
    ></input>
</template>

<script>

import field from '@/mixins/field';

  export default {
    mixins: [field],
	methods: {
        sendEmit(ev) {
            this.$emit('input', ev.target.value)
        },
	}
  }
</script>
