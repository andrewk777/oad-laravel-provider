const field = {
	props: ['data','value','index'],
	data() {
		return {
			options: []
		}
	},
	computed: {
		fieldName() {
			return this.data.name ? this.data.name : '';
		},
		fieldId() {
			return this.data.id ? this.data.id : this.data.name
		},
		placeholderText() {
			if (this.data.showPlaceholder)
				return this.data.placeholder ? this.data.placeholder : this.data.type =='select' ? 'Select' : '';
		},
		fieldClass() {
			if (this.data.type == 'select' || this.data.type == 'echeck') this.data.cssFormControl = false;
	        if (this.data.cssFormControl) {
				this.data.cssClass += ' form-control';
				this.data.cssFormControl = false;
			}
			if (this.data.type == 'color') {
				this.data.cssClass += ' pointer border-0';
			}
			return this.data.cssClass;
		},
		disabled() {
            return (typeof this.data.disabled !== 'undefined' && this.data.disabled) ? this.data.disabled : false;
        },
        fieldType() {
			if (this.data.type == 'number') return 'text' ;
            return this.data.type;
        }
	},
	methods: {
        obj2Options(obj) {
            var options = [];
            Object.keys(obj).forEach((key) => {
                options.push({
                    code: key,
                    label: obj[key]
                })
			});
            return options;
        },
		emitChild(val) {
			this.$emit('input', val)
		}
	}
}
export default field
