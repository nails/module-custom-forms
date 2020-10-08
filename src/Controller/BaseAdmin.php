<?php

/**
 * This class provides some common Custom Form controller functionality in admin
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Controller;

use Nails\Admin\Controller\Base;
use Nails\Common\Service\Asset;
use Nails\CustomForms\Constants;
use Nails\Factory;

/**
 * Class BaseAdmin
 *
 * @package Nails\CustomForms\Controller
 */
class BaseAdmin extends Base
{
    public function __construct()
    {
        parent::__construct();
        /** @var Asset $oAsset */
        $oAsset = Factory::service('Asset');
        $oAsset->load('admin.css', Constants::MODULE_SLUG);
    }
}
