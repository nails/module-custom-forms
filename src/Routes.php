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

/**
 * Class Routes
 *
 * @package Nails\CustomForms
 */
class Routes implements RouteGenerator
{
    /**
     * Returns an array of routes for this module
     *
     * @return string[]
     */
    public static function generate(): array
    {
        return [
            'forms(/(.+))?' => 'forms/index/$2',
        ];
    }
}
