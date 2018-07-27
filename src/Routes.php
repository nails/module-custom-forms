<?php

/**
 * Generates Form routes
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms;

use Nails\Common\Interfaces\RouteGenerator;

class Routes implements RouteGenerator
{
    /**
     * Returns an array of routes for this module
     * @return array
     */
    public static function generate()
    {
        return [
            'forms(/(.+))?' => 'forms/index/$2',
        ];
    }
}
