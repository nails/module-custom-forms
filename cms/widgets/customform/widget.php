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
     * Construct and define the widget
     */
    public function __construct()
    {
        parent::__construct();

        $this->label       = 'Custom Form';
        $this->grouping    = 'Custom Forms';
        $this->description = 'Render a custom form.';
        $this->keywords    = 'custom form, form';
    }
}
