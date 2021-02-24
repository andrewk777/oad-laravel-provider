
import _ from 'underscore';

const fieldData = {
    methods: {
        makeField(obj) {
            var params = {
                label: '',
                id: 'field',
                name: 'field',
                type: 'text',
                cssClass: '',
                placeholder: '',
                required: '',
                cssClass: '',
                allow_clear: true,
                confirm_on_delete: false,
                groupClass: '',
                mask: '',
                filters: [],
                cssFormControl: true,
                options: [],
                rows: 3,
                ajax: '',
                allowed_ext: '',
                can_delete: true,
                fieldset: true,
                showPlaceholder: true
            }
            obj.id = obj.id ?? obj.name
            _.extend(params, obj)
    
            return params;
        }
    }   
}
export default fieldData 