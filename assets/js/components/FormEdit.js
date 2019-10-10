class FormEdit {

    /**
     * Constructs FormEdit
     */
    constructor() {

        //  Generic elements
        this.dom = {};
        this.dom.$container = $('.group-custom-forms.edit');

        if (this.dom.$container.length) {

            //  Thank you elements
            this.dom.thankyou = {
                $container: $('#field-do-send-thankyou'),
                $checked: $('#field-do-send-thankyou input[type=checkbox]'),
                $options: $('#send-thankyou-options')
            }

            //  Field elements
            this.dom.fields = {
                $container: $('#custom-form-fields')
            }

            //  Notification elements
            this.dom.notification = {
                $container: $('#field-custom-form-notifications')
            };

            this.bindEvents();
            this.initialStates();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Binds events on load
     */
    bindEvents() {

        this.dom.thankyou.$container
            .on('toggle', (event, toggled) => {
                this.fieldDoSendThankYou(toggled);
            });

        this.dom.fields.$container
            .on('click', '.js-remove-field', (e) => {
                //  Timeout to allow the dom to be updated
                setTimeout(() => {
                    this.refreshNotifyConditionals(true);
                }, 50);
            })
            .on('blur', '.form-builder__field__label', () => {
                this.refreshNotifyConditionals();
            })
            .find('.js-add-field')
            .on('click', () => {
                this.refreshNotifyConditionals();
            });

        this.dom.notification.$container
            .on('dynamic-table:add', (e, $row) => {
                $row
                    .find('select.js-notification-condition-enabled')
                    .trigger('change');
                this.refreshNotifyConditionals();
            })
            .on('change', 'select.js-notification-condition-enabled', (e) => {
                this.toggleNotifyConditionals(
                    e.currentTarget.value === '1',
                    $(e.currentTarget).closest('td')
                )
            });
    }

    // --------------------------------------------------------------------------

    /**
     * Sets initial states
     */
    initialStates() {
        //  Initial state of thank you form
        this.fieldDoSendThankYou(this.dom.thankyou.$checked.is(':checked'));

        //  Initial state of conditionsls
        this.dom.notification.$container
            .find('select.js-notification-condition-enabled')
            .trigger('change');

        //  Ensure conditionals are populated
        this.refreshNotifyConditionals();

        //  Ensure conditionals are set correctly
        this.dom.notification.$container
            .find('select.js-notification-condition-field-id')
            .each((index, element) => {
                let $select = $(element);
                if ($select[0].hasAttribute('data-dynamic-table-value')) {
                    let value = $select.data('dynamic-table-value');
                    $('option[value="' + value + '"]', $select).prop('selected', true);
                    $select.trigger('change');
                }
            });
    }

    // --------------------------------------------------------------------------

    /**
     * Toggles the "do send thank you" form
     * @param toggled
     */
    fieldDoSendThankYou(toggled) {
        if (toggled) {
            this.dom.thankyou.$options.show();
        } else {
            this.dom.thankyou.$options.hide();
        }

        if (typeof window._nails === 'object') {
            window._nails.addStripes();
        }
    }

    // --------------------------------------------------------------------------

    toggleNotifyConditionals(toggled, $container) {

        $container
            .find([
                '.js-notification-condition-field-id',
                '.js-notification-condition-operator',
                '.js-notification-condition-value'
            ].join(', '))
            .prop('disabled', !toggled)
            .parent()
            .css({
                'opacity': !toggled ? 0.5 : 1,
                'pointer-events': !toggled ? 'none' : 'auto'
            });
    }

    // --------------------------------------------------------------------------

    refreshNotifyConditionals(resetOnFail) {

        let fields = [];
        this.dom.fields.$container.find('.form-builder__field')
            .each((index, row) => {
                let $row = $(row)
                fields.push({
                    id: $row.find('.form-builder__field__id').val(),
                    fieldNumber: $row.find('.form-builder__field__field-number').val(),
                    label: $row.find('.form-builder__field__label').val()
                })
            });

        let warnings = [];
        this.dom.notification.$container
            .find('select.js-notification-condition-field-id')
            .each((index, element) => {

                let $select = $(element);
                let $row = $select.closest('tr');
                let currentValue = $select.val();

                $select.find('option').remove()
                $.each(fields, (index, option) => {

                    let $option = $('<option>')
                        .attr('value', option.id || 'fieldNumber:' + option.fieldNumber)
                        .html(option.label);

                    $select.append($option);
                });

                //  If the selected item is no longer available, set the person to always receive email
                let $selectedOption = $select.find('option[value="' + currentValue + '"]');

                if ($selectedOption.length) {

                    $selectedOption.prop('selected', true);

                } else if (resetOnFail) {

                    $row.find('.js-notification-condition-enabled').val(0).trigger('change');

                    let email = $row.find('.js-notification-email').val();
                    warnings.push(email || 'The item at position ' + (index + 1));
                }
                $select.trigger('change');
            })

        if (warnings.length) {
            let message = 'The following notifications have been set to "Always" as their condition can no longer be satisfied:<br>'
            for (let i = 0; i < warnings.length; i++) {
                message += '<br>– ' + warnings[i];
            }

            $('<div>')
                .html(message)
                .dialog({
                    modal: true,
                    resizable: false,
                    draggable: false,
                    title: 'Notification conditions changed',
                    width: 500,
                    buttons: {
                        'OK': function() {
                            $(this).dialog('close');
                        }
                    }
                })
                .show();
        }
    }
}

export default FormEdit;

