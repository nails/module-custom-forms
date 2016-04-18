/* globals _nails */
var _ADMIN_CUSTOMFORM_EDIT;
_ADMIN_CUSTOMFORM_EDIT = function()
{
    /**
     * Avoid scope issues in callbacks and anonymous functions by referring to `this` as `base`
     * @type {Object}
     */
    var base = this;

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

        //  Initial states
        base.fieldDoSendThankYou($('#field-do-send-thankyou input[type=checkbox]').is(':checked'));

        return base;
    };

    // --------------------------------------------------------------------------

    base.fieldDoSendThankYou = function(toggled) {

        if (toggled) {

            $('#send-thankyou-options').show();

        } else {

            $('#send-thankyou-options').hide();
        }

        if (typeof _nails === 'object') {
            _nails.addStripes();
        }
    };

    // --------------------------------------------------------------------------

    return base.__construct();
}();
