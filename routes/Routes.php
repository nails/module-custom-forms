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

namespace Nails\Routes\Forms;

use Nails\Common\Model\BaseRoutes;

class Routes extends BaseRoutes
{
    /**
     * Returns an array of routes for this module
     *
     * @return array
     */
    public function getRoutes()
    {
        return [
            'forms/(.*)' => 'forms/index/$1',
        ];
    }
}
