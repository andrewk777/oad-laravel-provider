<template>
	<enhanced-check
		:id="fieldId"
		:name="fieldName"
		v-model="fvalue"
		:rounded="false"
		:label="data.placeholder"
		:subClass="fieldClass"
		:disabled="disabled"

	></enhanced-check>
</template>

<script>
import { EnhancedCheck } from 'vue-enhanced-check'
import field from '@/mixins/field';

export default {
	components: {
		EnhancedCheck
	},
	mixins: [field],
	computed: {
		fvalue: {
			get () {
				return this.value
			},
			set (val) {
				this.$emit('input', val)
			}
		}
	},
	mounted() {
		this.$set(this,'value',this.value)
	}
}
</script>