<?php

/**
 * Manage form notifications
 *
 * @package     Nails
 * @subpackage  module-custom-form
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Model\Form;

use Nails\Common\Model\Base;

/**
 * Class Notification
 *
 * @package Nails\CustomForms\Model
 */
class Notification extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'custom_form_notification';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'FormNotification';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = 'nails/module-custom-forms';
}
