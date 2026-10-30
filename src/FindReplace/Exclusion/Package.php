<?php

namespace Nails\CustomForms\FindReplace\Exclusion;

use Nails\FindReplace\Interfaces\Exclusion;

class Package implements Exclusion
{
    public function hidden(): array
    {
        return [
            //  `answers` holds what people submitted; personal data, not content to rewrite.
            NAILS_DB_PREFIX . 'custom_form_response',
        ];
    }

    public function readOnly(): array
    {
        return [
            NAILS_DB_PREFIX . 'custom_form.form_attributes',
            NAILS_DB_PREFIX . 'custom_form.cta_attributes',
            NAILS_DB_PREFIX . 'custom_form_notification.condition_value',
        ];
    }
}
