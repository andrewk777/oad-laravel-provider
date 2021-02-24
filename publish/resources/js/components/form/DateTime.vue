<template>
    <VueCtkDateTimePicker
        :no-value-to-custom-elem="true"
        ref="dateTimePicker"
        :id="data.id"
        v-model="compValue"
        color="#564AA3"
        :no-label="true"
        :min-date="data.min"
        :format="formatted"
        minuteInterval="15"
        :first-day-of-week="firstday"
        :noClearButton="noClearButton"
        :no-button-now="true"
        />
</template>

<script>
    import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';

    import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css';
    import field from '@/mixins/field';
    import moment from 'moment'
            import { CONF } from '@/config';

    export default {
        components: {
            VueCtkDateTimePicker
        },

        data() {
            return {
                compValue: this.value,
                formatted: CONF.DB_MOMENTS_TIME,
                firstday: 1
            }
        },
        computed: {
            noClearButton() {
                return this.data.required.length > 0;
            }
        },
        methods: {
            clickDetection(event) {
                if (!this.$el.contains(event.target)) {
                    this.$refs.dateTimePicker.closePicker()
                }
            }
        },
        watch: {
            compValue(newState, oldState) {
                if (newState != oldState && newState != this.value) {
                    newState = newState ? moment(newState).format(CONF.DB_MOMENTS_TIME) : '';
                    this.$emit('input', newState)
                }
            },
            value(newState) {
                this.compValue = newState;
            }
        },
        mounted() {
            document.addEventListener('mouseup', this.clickDetection)
        },
        destroyed() {
            document.removeEventListener('mouseup', this.clickDetection)
        },
        mixins: [field]
    }
</script>
