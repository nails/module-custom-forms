<?php

/**
 * This is the "Customform" CMS template definition
 *
 * @package     Nails
 * @subpackage  module-custom-form
 * @category    Template
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Cms\Template;

class Customform extends TemplateBase
{
    /**
     * Defines the basic template details object.
     * @return stdClass
     */
    public static function details()
    {
        //  Base object
        $d = parent::details();

        //  Basic details; describe the template for the user
        $d->label       = 'Custom Form';
        $d->description = 'A full width template which renders a custom form.';

        /**
         * Widget areas; give each a unique index, the index will be passed as the
         * variable to the view
         */

        $d->widget_areas['mainbody']        = parent::editableAreaTemplate();
        $d->widget_areas['mainbody']->title = 'Main Body';

        // --------------------------------------------------------------------------

        return $d;
    }
}
