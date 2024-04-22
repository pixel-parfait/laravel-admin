import moment from 'moment'

export default {
    formatDate(value) {
        if (!value) {
            return ''
        }

        let date = moment(value),
            format = date.creationData().format

        if (format === 'YYYY') {
            return date.format('YYYY')
        } else if (format === 'YYYY-MM') {
            return date.format('MM/YYYY')
        }

        return date.format('DD/MM/YYYY')
    },
    formatDateTime(value) {
        if (!value) {
            return ''
        }

        return moment(value).format('DD/MM/YYYY - HH:mm')
    }
}
