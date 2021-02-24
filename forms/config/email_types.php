<?php

use Nails\CustomForms\Constants;

/**
 * This config file defines email types for this module.
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Config
 * @author      Nails Dev Team
 * @link
 */

$config['email_types'] = [
    (object) [
        'slug'             => 'custom_form_submitted',
        'name'             => 'Custom Forms: Form Submitted',
        'description'      => 'Sent to nominated email when a form is submitted.',
        'template_header'  => '',
        'template_body'    => 'forms/email/form_submitted',
        'template_footer'  => '',
        'default_subject'  => 'The {{label}} form has been submitted',
        'can_unsubscribe'  => false,
        'factory'          => Constants::MODULE_SLUG . '::EmailFormSubmitted',
    ],
    (object) [
        'slug'             => 'custom_form_submitted_thanks',
        'name'             => 'Custom Forms: Thanks for submitting form',
        'description'      => 'Sent to user if they are logged in when they submit a form.',
        'template_header'  => '',
        'template_body'    => 'forms/email/form_submitted_thanks',
        'template_footer'  => '',
        'default_subject'  => '{{subject}}',
        'can_unsubscribe'  => false,
        'factory'          => Constants::MODULE_SLUG . '::EmailFormSubmittedThanks',
    ],
];
