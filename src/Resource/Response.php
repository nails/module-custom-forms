<?php

/**
 * The response resource
 *
 * @package     Nails
 * @subpackage  module-custom-form
 * @category    Rsource
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Resource;

use Nails\Common\Resource\Entity;
use Nails\CustomForms\Resource;

/**
 * Class Response
 *
 * @package Nails\CustomForms\Resource
 */
class Response extends Entity
{
    /** @var int */
    public $form_id;

    /** @var Resource\Form */
    public $form;

    /** @var \stdClass[] */
    public $answers;

    /** @var bool */
    public $is_deleted;
}
