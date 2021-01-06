<?php

namespace Nails\CustomForms\Factory\Email\Form\Submitted;

use Nails\Email\Factory\Email;

/**
 * Class Thanks
 *
 * @package Nails\CustomForms\Factory\Email\Form\Submitted
 */
class Thanks extends Email
{
    /**
     * The email's type
     *
     * @var string
     */
    protected $sType = 'custom_form_submitted_thanks';

    // --------------------------------------------------------------------------

    /**
     * Returns test data to use when sending test emails
     *
     * @return array
     */
    public function getTestData(): array
    {
        return [
            'subject' => 'Tellus Amet Fringilla Tristique',
            'body'    => 'Donec ullamcorper nulla non metus auctor fringilla. Donec id elit non mi porta gravida at eget metus.',
        ];
    }
}
