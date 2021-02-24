<template>
	<textarea
		:id="fieldId"
	    :name="fieldName"
	    :value="value"
	    :class="fieldClass"
	    :placeholder="placeholderText"
		:rows="data.rows"
	    @input="sendEmit"
	    :disabled="disabled"> {{ data.cssClass }}
	</textarea>
</template>

<script>

import field from '@/mixins/field';

  export default {
    mixins: [field],
    computed: {
        disabled() {
           return (typeof this.data.disabled !== 'undefined' && this.data.disabled) ? this.data.disabled : false;
        }
    },
	methods: {
        sendEmit(ev) {
            this.$emit('input', ev.target.value)
        }
	}
  }
</script>
