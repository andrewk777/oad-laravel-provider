<template>

	<v-select
		:id="fieldId"
		:name="fieldName"
		:options="obj2Options(options)"
		:getOptionLabel="option => option.label"
		:value="valObj"
		:clearable="data.allow_clear"
		:placeholder="data.placeholder"
		:filterable="!useAjax"
		@open="onOpen"
		@input="selected"
		@search="onSearch"
		@close="$emit('close')"
		:class="data.cssClass"
		:disabled="disabled"
	>
			<span slot="spinner" v-show="showLoader" class="select-loader"><i class="fad fa-circle-notch fa-spin fa-2x"></i></span>

	<template v-slot:no-options="{ search, searching }">
		<span v-if="!showLoader">
			No results found for <em>{{ search }}</em>.
		</span>
		<em v-else style="opacity: 0.5;">Loading...</em>
	</template>
	</v-select>

</template>

<script>
import vSelect from 'vue-select'
import field from '@/mixins/field';
import _ from 'lodash';


export default {
	components: { vSelect },
	mixins: [field],
	data() {
		return {
			options: this.data.options,
			showLoader: false,
			valObj: null
		}
	},
	computed: {
		useAjax() {
			return this.data.ajax.length > 0
		}
	},
	watch: {
		value(val) {
			if (this.useAjax) {
				this.getAjaxLabel(val)
			} else {
				this.valObj = { code: val, label: this.options[val] }
			}
		}
	},
	methods: {
		onOpen() {
			if (this.useAjax) {
				this.showLoader = true;
				this.options = {};
				axios.post(this.data.ajax)
				.then((res) => {
					this.options = res.data;
					if (this.valObj) this.options[this.valObj.code] = this.valObj.label;
					this.showLoader = false;
				})
			}
		},
		onSearch(search, loading) {
			
			if (search.length) { // to escape function trigger after select
				this.options = {};
				this.showLoader =  true;
				this.search(loading, search, this);
			}
			
		},
		search: _.debounce((loading, search, vm) => {
			axios.post(vm.data.ajax, { search: search } )
			.then((res) => {
				vm.options = res.data;
				vm.showLoader = false;
			});
		}, 350),
		selected(val) {
			this.valObj = val
			if (val) {
				this.$emit('input', val.code)
				this.$emit('change', val ? { code: val.code , label: val.label } : { code: '' , label: '' } )
			} else {
				this.$emit('input', val)
				this.$emit('change', { code: '' , label: '' } )
			}
		},
		getAjaxLabel(val) {
			if (val) {
				this.showLoader = true;
				axios.post(this.data.ajax, { hash: val })
				.then((res) => {
					this.options = res.data;
					this.valObj = { code: val, label: res.data[val] }
					this.showLoader = false;
				})
			}
		}
	},
	mounted() {
		if (this.value) {
			if (this.useAjax) {
				this.getAjaxLabel(this.value)
			} else {
				this.valObj = { code: this.value, label: this.options[this.value] }
			}
		}
	}
	
}
</script>