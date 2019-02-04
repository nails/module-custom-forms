class FormEdit {
    constructor() {
        if ($('.group-custom-forms.edit').length) {
            //  Basic bindings
            $('#field-do-send-thankyou')
                .on('toggle', (event, toggled) => {
                    this.fieldDoSendThankYou(toggled);
                });

            //  Initial states
            this.fieldDoSendThankYou($('#field-do-send-thankyou input[type=checkbox]').is(':checked'));
        }
    }

    // --------------------------------------------------------------------------

    fieldDoSendThankYou(toggled) {
        if (toggled) {
            $('#send-thankyou-options').show();
        } else {
            $('#send-thankyou-options').hide();
        }

        if (typeof window._nails === 'object') {
            window._nails.addStripes();
        }
    }
}

export default FormEdit;

