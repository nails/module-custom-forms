<?php

/**
 * The form notification resource
 *
 * @package     Nails
 * @subpackage  module-custom-form
 * @category    Rsource
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Resource\Form;

use Nails\Common\Resource\Entity;

/**
 * Class Notification
 *
 * @package Nails\CustomForms\Resource\Form
 */
class Notification extends Entity
{
    /** @var int */
    public $form_id;

    /** @var string */
    public $email;

    /** @var bool */
    public $condition_enabled;

    /** @var int */
    public $condition_field_id;

    /** @var string */
    public $condition_operator;

    /** @var string */
    public $condition_value;
}
