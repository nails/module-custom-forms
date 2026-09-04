<?php

/**
 * Validates the admin create/edit custom form
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Validator
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Validator;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Factory\Service\FormValidation\Validator;
use Nails\Common\Service\FormValidation;
use Nails\Common\Validation\Context;
use Nails\CustomForms\Constants;
use Nails\CustomForms\Model;
use Nails\CustomForms\Resource;
use Nails\Factory;

class Form extends Validator
{
    /**
     * @param Resource\Form|null $oForm The form being edited, if any (its own slug is not a collision)
     */
    public function __construct(private readonly ?Resource\Form $oForm = null)
    {
        parent::__construct();
    }

    // --------------------------------------------------------------------------

    /**
     * @throws FactoryException
     */
    protected function rules(): array
    {
        /** @var Model\Form $oFormModel */
        $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);

        return [
            'label'              => [FormValidation::RULE_REQUIRED],
            'slug'               => [
                $this->oForm
                    ? FormValidation::rule(FormValidation::RULE_UNIQUE_IF_DIFF, $oFormModel->getTableName(), 'slug', $this->oForm->slug)
                    : FormValidation::rule(FormValidation::RULE_IS_UNIQUE, $oFormModel->getTableName(), 'slug'),
            ],
            'thankyou_page_title' => [
                function ($sTitle, Context $oContext) {
                    if (empty(trim((string) $sTitle)) && empty(json_decode((string) $oContext->getValue('thankyou_page_body')))) {
                        throw new ValidationException('Thank you page title is required if no body is set.');
                    }
                },
            ],
            'thankyou_page_body'  => [
                function ($mBody, Context $oContext) {
                    if (empty(json_decode((string) $mBody)) && empty(trim((string) $oContext->getValue('thankyou_page_title')))) {
                        throw new ValidationException('Thank you page body is required if no title is set.');
                    }
                },
            ],
        ];
    }
}
