import moment from 'moment'
import { CONF } from '@/config';

const mixin = {
	methods: {
        YesNo(value) {
            return value && value != 0 ? 'Yes' : 'No';
        },
        YesNoCheck(val) {
            return val && val != 0 ? '<i class="fas fa-check"></i>' : ''
        },
        dateFormat(value) {
            return value ? moment(value).format(CONF.DF_MOMENTS) : '-';
        },
		monthFormat(value) {
			return value ? moment(value).format('MMMM') : '-';
		},
        dateTimeFormat(value) {
            return value ? moment(value).format(CONF.MOMENT_DATETIME_HUMAN) : '-';
        },
        priceFormat(value) {
            return '$' + this.display_number(value);
        },
        priceFormatNoZero(value) {
            return value ? '$' + this.display_number(value) : '-';
        },
        percentFormat(value) {
            return '%' + this.display_number(value);
        },
        display_number: function(number, prefix, postfix, useGrouping) {
            if (typeof prefix == 'undefined') prefix = '';
            if (typeof postfix == 'undefined') postfix = '';
            if (typeof useGrouping == 'undefined') useGrouping = true;
            number = number * 1;
            return prefix + number.toLocaleString(undefined, {
                useGrouping: useGrouping,
                maximumFractionDigits: 2,
                minimumFractionDigits: 2
            }, ) + postfix;
        },
        phoneFormat(value) {
            if (value && value.length == 10) {
                var cleaned = ('' + value).replace(/\D/g, '')
                var match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/)
                return '(' + match[1] + ') ' + match[2] + '-' + match[3]
            } else if (value) {
                return value;
            }
            return '-';  
        },
        capitalize(value) {
            return value ? value.charAt(0).toUpperCase() + value.slice(1) : ''
        },
        toUpper(value) {
            return value.toUpperCase();
        },
        secondsToTime(value) {
            return moment.unix(value).utc().format('HH:mm:ss');
        }
    }
}
export default mixin
