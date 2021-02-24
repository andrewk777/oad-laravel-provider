
const panel = {
    data() {
        return {
            cssCloseFormActivate: false, //this variable is used to display panel closing animation
        }
    },
    computed: {
        wrapperClasses() {
            var obj = { closeForm: this.cssCloseFormActivate } //add animation class to panel to close
            obj[this.pageClass] = true;
            return obj
        }
    },
    methods: {
        closePanel() {
            this.cssCloseFormActivate = true;
            setTimeout(() => this.$router.go(-1), 500)

            if (typeof this.formDataChanged !== 'undefined' && this.formDataChanged) {
                //confirm that you want to leave the page
            }
            
            var parent = this.$store.getters.getFormParams[this.formId].parent;
            //refresh table if form was opened from a table
            if (this.formSaved && parent.type == 'table') {
                this.$parent.tableRefresh(parent.ref)
            }
        }
    }

}
export default panel
