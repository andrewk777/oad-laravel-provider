import Vue from "vue";
import btn from '@/components/form/btn'
import DropdownMenu from '@innologica/vue-dropdown-menu' //revise and delete?
import FieldSet from '@/components/form/Field-Set';
import FieldSetText from '@/components/form/Field-Set-Text';
import FieldText from '@/components/form/Text';
import FieldDate from '@/components/form/Date';
import FieldDateTime from '@/components/form/DateTime';
import FieldSelect from '@/components/form/Select';
import FieldToggle from '@/components/form/Toggle';
import FieldTextArea from '@/components/form/Textarea';
import FieldHidden from '@/components/form/Hidden';
import FieldEcheck from '@/components/form/Echeck';
import FieldFile from '@/components/form/File';

Vue.component('dropdown-menu',DropdownMenu)
Vue.component('btn',btn);
Vue.component('f-set', FieldSet)
Vue.component('f-set-text', FieldSetText)
Vue.component('f-text', FieldText)
Vue.component('f-date', FieldDate)
Vue.component('f-datetime', FieldDateTime)
Vue.component('f-select', FieldSelect)
Vue.component('f-toggle', FieldToggle)
Vue.component('f-textarea', FieldTextArea) 
Vue.component('f-hidden', FieldHidden)
Vue.component('f-echeck', FieldEcheck)
Vue.component('f-file', FieldFile)