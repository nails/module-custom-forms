/* globals _nails, Mustache */
var _ADMIN_CUSTOM_FORMS_EDIT;
_ADMIN_CUSTOM_FORMS_EDIT = function(typeWithOptions, typeWithDefaultValue)
{
    /**
     * Avoid scope issues in callbacks and anonymous functions by referring to `this` as `base`
     * @type {Object}
     */
    var base = this;

    // --------------------------------------------------------------------------

    base.tplField = $('#template-field').html();
    base.tplOptionContainer = $('#template-field-option-container').html();
    base.tplOption = $('#template-field-option').html();

    // --------------------------------------------------------------------------

    base.fieldCount           = 0;
    base.typeWithOptions      = typeWithOptions || [];
    base.typeWithDefaultValue = typeWithDefaultValue || [];

    // --------------------------------------------------------------------------

    /**
     * Construct the class
     * @return {Void}
     */
    base.__construct = function()
    {
        //  Basic bindings
        $('#field-do-send-thankyou').on('toggle', function(event, toggled) {
            base.fieldDoSendThankYou(toggled);
        });

        //  Field bindings
        $('#form-fields').on('change', '.field-type', function() {
            base.fieldTypeChanged($(this));
        });

        $('#form-fields').on('change', '.field-default', function() {
            base.fieldDefaultChanged($(this));
        });

        $('#add-field').on('click', function() {
            base.addField();
            return false;
        });

        $('#form-fields').on('click', '.remove-field', function() {
            base.removeField($(this));
            return false;
        });

        $(document).on('click', '.add-option', function() {
            base.addOption($(this));
        });

        $(document).on('click', '.remove-option', function() {
            base.removeOption($(this));
            return false;
        });

        $('#main-form').on('submit', function() {
            return base.validate();
        });

        // --------------------------------------------------------------------------

        //  Initial states
        base.fieldDoSendThankYou($('#field-do-send-thankyou input[type=checkbox]').is(':checked'));

        $('#form-fields td.type .field-type, #form-fields td.default .field-default').each(function() {
            $(this).trigger('change');
        });

        base.fieldCount = $('#form-fields tbody tr').length-1;

        // --------------------------------------------------------------------------

        //  Sortables
        $('#form-fields tbody').sortable({
            'handle': '.handle',
            'axis': 'y',
            'containment': 'parent',
            'placeholder': 'placeholder',
            'helper': function(e, tr) {

                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {

                    // Set helper cell sizes to match the original sizes
                    $(this).width($originals.eq(index).outerWidth());
                });
                $helper.css({
                    'margin-left':'-1px',
                    'box-shadow': '0px 2px 5px rgba(0, 0, 0, 0.25)'
                });
                return $helper;
            },
            'start': function(e, ui) {

                ui.placeholder.height(ui.helper.outerHeight());
            }
        });
    };

    // --------------------------------------------------------------------------

    base.fieldDoSendThankYou = function(toggled) {

        if (toggled) {

            $('#send-thankyou-options').show();

        } else {

            $('#send-thankyou-options').hide();
        }

        if (typeof _nails === 'function') {
            _nails.addStripes();
        }
    };

    // --------------------------------------------------------------------------

    base.addField = function() {

        base.fieldCount++;

        var tpl, tplData;
        tplData = {
            'fieldNumber': base.fieldCount
        };
        tpl = Mustache.render(base.tplField, tplData);
        $('#form-fields tbody').append(tpl);

        tpl = Mustache.render(base.tplOptionContainer, tplData);
        $('#field-options').append(tpl);

        //  Init the select2's
        $('#form-fields tbody tr').last().find('.select2').select2();
    };

    // --------------------------------------------------------------------------

    base.removeField = function(elem) {

        var fieldNumber = elem.data('field-number');
        elem.closest('tr').remove();
        $('#form-field-options-' + fieldNumber).remove();
    };

    // --------------------------------------------------------------------------

    base.addOption = function(elem) {

        var table, tpl, tplData;
        table = elem.parent().parent().find('table');
        tplData = {
            'fieldNumber': elem.data('field-number'),
            'optionNumber': table.data('option-count') || 0
        };
        tpl = Mustache.render(base.tplOption, tplData);
        $('#form-field-options-' + tplData.fieldNumber + ' tbody').append(tpl);

        table.data('option-count', tplData.optionNumber+1);
        $.fancybox.update();
    };

    // --------------------------------------------------------------------------

    base.removeOption = function(elem) {

        elem.closest('tr').remove();
        $.fancybox.update();
    };

    // --------------------------------------------------------------------------

    base.fieldTypeChanged = function(elem) {

        //  Supports options?
        if ($.inArray(elem.val(), base.typeWithOptions) >= 0) {
            elem.siblings('a').addClass('is-active');
        } else {
            elem.siblings('a').removeClass('is-active');
        }

        //  Supports default value?
        if ($.inArray(elem.val(), base.typeWithDefaultValue) >= 0) {
            elem.closest('tr').find('td.default .js-supports-default-value').show();
            elem.closest('tr').find('td.default .js-no-default-value').hide();
        } else {
            elem.closest('tr').find('td.default .js-supports-default-value').hide();
            elem.closest('tr').find('td.default .js-no-default-value').show();
        }
    };

    // --------------------------------------------------------------------------

    base.fieldDefaultChanged = function(elem) {

        switch (elem.val()) {

            case 'CUSTOM' :
                elem.siblings('input').addClass('is-active');
                break;

            default :
                elem.siblings('input').removeClass('is-active');
                break;
        }
    };

    // --------------------------------------------------------------------------

    base.validate = function() {
        return true;
    };

    // --------------------------------------------------------------------------

    return base.__construct();
};