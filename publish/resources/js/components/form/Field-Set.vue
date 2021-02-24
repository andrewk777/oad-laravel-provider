<template>
<fieldset class="fieldset" :class="cssClass" :id="wrapID">
	<legend v-if="fieldLabel" v-html="fieldLabel"></legend>
	<div class="field-controls">
		<slot name="controls"></slot>
	</div>
	<div class="clearfix"></div>
	<f-text v-if="data.type == 'text' || data.type == 'number' || data.type == 'password' || data.type == 'autosuggest' || data.type == 'color'"
		:value="value" @input="emitChild" @keyup="emitKeyup" :data="data" :index="setIndex" />
	<f-date v-if="data.type == 'date' || data.type == 'daterange'"  @change="emitChange" :value="value" @input="emitChild" :data="data" :index="setIndex" />
	<f-select v-if="data.type == 'select'" @close="$emit('close')" :value="value" @input="emitChild" @change="emitChange" :data="data" :index="setIndex" />
	<f-toggle v-if="data.type == 'toggle'" :value="value" @input="emitChild" :data="data" :index="setIndex" />
	<f-textarea v-if="data.type == 'textarea'" :value="value" @input="emitChild" :data="data" :index="setIndex" />
	<f-echeck v-if="data.type == 'echeck'" :value="value" @input="emitChild" :data="data" :index="setIndex" />
	<f-file v-if="data.type == 'file'" :value="value" @input="emitChild" :data="data" :index="setIndex" @file-removed="fileRemoved" />
</fieldset>
</template>

<script>

export default {
	props: ['value','data','index','cssClass'],
	computed: {
		wrapID() {
			var id = this.data.id ? this.data.id : this.data.name;
			return id ? id + '-wrap' : '';
		},
		fieldLabel() {
			var required = this.data.required ? '<span class="required-asterisk">*</span>' : '';
			return this.data.label ? this.data.label + required : '';
		},
		setIndex() {
			return typeof this.index !== 'undefined' ? this.index : 0;
		}
	},
	methods: {
		emitChild(val) {
			this.$emit('input', val)
		},
		emitKeyup(val) {
			this.$emit('keyup', val)
		},
		emitChange(val) {
			this.$emit('change', val)
		},
		fileRemoved(obj) {
			this.$emit('file-removed',obj)
		}
	}
}
</script>
