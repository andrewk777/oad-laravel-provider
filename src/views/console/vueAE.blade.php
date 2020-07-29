<template>
    <div class="slide-out-content">
         <component-loader v-if="showLoader"></component-loader>
         <div class="panel-header">
             <div class="panel-title">
                 @{{ formTitle }}
             </div>
             <div class="panel-btns">
                <b-button variant="outline-success" @click="saveForm(true)"><i class="fal fa-save"></i><span class="d-none d-sm-inline"> Save</span></b-button>
                <b-button variant="outline-warning" @click="closePanel"><i class="fal fa-times"></i><span class="d-none d-sm-inline"> Close</span></b-button>
             </div>
         </div>
         <div v-if="typeof f === 'object'" class="module-content">
             <form id="aeForm"  @submit.prevent="saveForm">
                 <button type="submit" class="d-none"></button> <!-- to trigger enter key -->
                 <div class="panel panel-default">
                     <b-row>
        				 <b-col>
                 			@foreach ($form_fields as $field)
                 				<f-set v-model="fields.{{ $field['name'] }}" :data="f.{{ $field['name'] }}"/>
                 			@endforeach
        				 </b-col>
                     </b-row>
                 </div>
             </form>
         </div>
    </div>

</template>

<script>

import ComponentLoader from '@src/components/Loader';
import form from '@src/mixins/form';

export default {
    components: { ComponentLoader },
    mixins: [form],
    data() {
        return {
            pageTitle: '{{ $page_title_single }}',
            formAction: '{{ $base_api_route }}/save'
        }
    }

}
</script>
