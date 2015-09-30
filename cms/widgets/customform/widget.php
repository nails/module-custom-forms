<?php

/**
 * This class is the "Custom Form" CMS widget definition
 *
 * @package     Nails
 * @subpackage  module-custom-form
 * @category    CMS Widget
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Cms\Widget;

class Customform extends WidgetBase
{
    /**
     * Defines the basic widget details object.
     * @return stdClass
     */
    public static function details()
    {
        $d              = parent::details();
        $d->label       = 'Custom Form';
        $d->description = 'Render a custom form.';
        $d->keywords    = 'custom form, form';

        return $d;
    }
}
