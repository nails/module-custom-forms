<?php

namespace Nails\CustomForms\Factory\Email\Form;

use Nails\Email\Factory\Email;

/**
 * Class Submitted
 *
 * @package Nails\CustomForms\Factory\Email\Form
 */
class Submitted extends Email
{
    /**
     * The email's type
     *
     * @var string
     */
    protected $sType = 'custom_form_submitted';

    // --------------------------------------------------------------------------

    /**
     * Returns test data to use when sending test emails
     *
     * @return array
     */
    public function getTestData(): array
    {
        return [
            'label'  => 'The form\'s label',
            'answers' => [
                [
                    'field'    => (object) [
                        'id'   => 123,
                        'type' => '\\Class\\Name',
                    ],
                    'question' => 'The question',
                    'answer'   => 'The answer',
                ],
            ],
        ];
    }
}
