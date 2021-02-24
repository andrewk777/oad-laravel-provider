
const form = {
    data() {
        return {
            pageTitle: '', //page title
            formUri: '', //uri base for api for this form
            formSaveUri: false,
            formId: '', //unique id of the form
            loading: true,
            formReady: false,
            pageClass: '', //css class of the component
            hash: '', //form hash
            storedFormParams: {}, //data from vuex stored under form id
            forms: {},  //current active forms
            dataWithSaveRequest: {}, //additional data sent with save request
            fromsOriginal: {}, //froms before unsaved changes
            pageData: {}, // any additional data sent along with form data
            formDataChanged: false, //if any data has been changed in the form and not saved
            formSaved: false, //indicates if the form has been saved at least once
            beforeSaveFunction: false, //function triggered before sending axios request with form data
            afterSaveFunction: false //function triggered on form save axios reponse
        }
    },
    watch: {
        forms(val) {
            if (val != this.fromsLoaded) {
                this.formDataChanged = true
            } else {
                this.formDataChanged = false
            }
        }

    },
    computed: {
        addEditWord() {
            return this.hash.length == 0 ? 'Add' : 'Edit'
        },
        isNewForm() {
            return this.hash.length == 0;
        },
        isExistingForm() {
            return this.hash.length != 0;
        },
        formUriParams() {
			var params = {};
			params.hash = this.hash;

            if (typeof this.storedFormParams === 'object' && 
                    typeof this.storedFormParams.defaultValues === 'object' && 
                    Object.keys(this.storedFormParams.defaultValues).length) {
                
				var defValues = this.storedFormParams.defaultValues;
				for (var i = 0; i < defValues.length; i++) {
					params[defValues[i].param] = defValues[i].value 
				}
			}
			return params;
	    },
    },
    methods: {
        saveForm(closeOnSave,addNewFunc='') {

            this.loading = true; 

            if (this.beforeSaveFunction) {
				this[this.beforeSaveFunction]()
            }
            this.formSaveUri = this.formSaveUri ? this.formSaveUri: this.formUri + '/save'

            axios.post(this.formSaveUri, {
                hash: this.hash,
                forms: this.forms,
                data: this.dataWithSaveRequest
            })
            .then((res) => {

                this.$utils.c_notify(res.data.res, res.data.status);
                this.loading = false;

                if (res.data.status == 'success') {

                    this.hash = typeof res.data.hash !== 'undefined' ? res.data.hash : '';
                    this.formSaved = true;
                    this.fromsOriginal = this.forms
                    this.formDataChanged = false;
                   
                    if (this.afterSaveFunction) {
                        this[this.afterSaveFunction](res.data)
                    } else if (closeOnSave) {
                         this.closePanel();
                    } else if (addNewFunc.length) {
                        this[addNewFunc]();
                    }

                }
                
            })           
        },
        resetFormValues() {
            for (var field in this.forms.main.values) {
                if (this.forms.main.values.hasOwnProperty(field)) {
                    this.forms.main.values[field] = '';
                    this.hash = '';
                }
            }
        }
    },
    mounted() {

        this.hash = (typeof this.$route.params.hash !== 'undefined' && this.$route.params.hash != 0 )? this.$route.params.hash: '';
        this.storedFormParams = this.$store.getters.getFormParams[this.formId];

        if (this.formUri.length) {
            axios({
                url: 	this.formUri,
                method:	'get',
                params:  this.formUriParams
            })
            .then((res) => {
              
                if (res) { // is false if access is denied
                    if (typeof res.data.forms !== 'undefined')
                    this.forms = this.fromsOriginal = res.data.forms
                
                    if (typeof res.data.data !== 'undefined')
                        this.pageData = res.data.data

                        this.loading = false;
                        this.formReady = true;
                }
    
                
    
            })

        } else {

            this.loading = false;
            this.formReady = true;

        }

        
    }
}

export default form