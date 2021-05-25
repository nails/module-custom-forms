<?php

/**
 * The form resource
 *
 * @package     Nails
 * @subpackage  module-custom-form
 * @category    Rsource
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Resource;

use Nails\Common\Resource\Entity;
use Nails\Common\Resource\ExpandableField;
use Nails\CustomForms\Resource;

/**
 * Class Form
 *
 * @package Nails\CustomForms\Resource
 */
class Form extends Entity
{
    /** @var string */
    public $slug;

    /** @var int */
    public $form_id;

    /** @var Resource\Form|null */
    public $form;

    /** @var string */
    public $label;

    /** @var string */
    public $header;

    /** @var string */
    public $footer;

    /** @var string */
    public $cta_label;

    /** @var string */
    public $cta_attributes;

    /** @var string */
    public $form_attributes;

    /** @var bool */
    public $thankyou_email;

    /** @var string */
    public $thankyou_email_subject;

    /** @var string */
    public $thankyou_email_body;

    /** @var string */
    public $thankyou_page_title;

    /** @var string */
    public $thankyou_page_body;

    /** @var bool */
    public $is_minimal;

    /** @var bool */
    public $is_deleted;

    /** @var ExpandableField|null */
    public $notifications;

    /** @var ExpandableField|null */
    public $responses;
}
