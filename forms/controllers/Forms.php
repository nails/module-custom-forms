<?php

/**
 * This class handles form submissions
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

use App\Controller\Base;
use Nails\Common\Exception\ValidationException;
use Nails\Email;
use Nails\Factory;

class Forms extends Base
{
    public function index()
    {
        $oUri              = Factory::service('Uri');
        $oInput            = Factory::service('Input');
        $oFormModel        = Factory::model('Form', 'nails/module-custom-forms');
        $oFieldTypeService = Factory::service('FieldType', 'nails/module-form-builder');
        $oCaptcha          = Factory::service('Captcha', 'nails/module-captcha');

        Factory::helper('formbuilder', 'nails/module-form-builder');

        $sFormSlug         = $oUri->rsegment(3);
        $bIsCaptchaEnabled = $oCaptcha->isEnabled();
        $oForm             = $oFormModel->getBySlug(
            $sFormSlug,
            [
                'expand' => [
                    [
                        'form',
                        [
                            'expand' => [
                                [
                                    'fields',
                                    ['expand' => ['options']],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        if (empty($oForm)) {
            show404();
        }

        $sHeaderView = $oForm->is_minimal ? 'structure/header/blank' : 'structure/header';
        $sFooterView = $oForm->is_minimal ? 'structure/footer/blank' : 'structure/footer';

        if ($oInput->post()) {
            try {

                if (!formBuilderValidate($oForm->form->fields->data, $oInput->post('field'))) {
                    throw new ValidationException(lang('fv_there_were_errors'));
                }

                if ($oForm->form->has_captcha && $bIsCaptchaEnabled) {
                    if (!$oCaptcha->verify()) {
                        throw new ValidationException('You failed the captcha test');
                    }
                }

                //  Save the response
                $aData = [
                    'form_id' => $oForm->id,
                    'answers' => [],
                ];

                /**
                 * Build the answer array; this should contain the text equivilents of all fields so that
                 * should the parent form change, the answers won't be affected
                 */
                $bIsFormValid = true;
                foreach ($oForm->form->fields->data as &$oField) {
                    $oFieldType = $oFieldTypeService->getBySlug($oField->type);
                    if (!empty($oFieldType)) {
                        try {

                            $mAnswer = !empty($_POST['field'][$oField->id]) ? $_POST['field'][$oField->id] : null;

                            $aData['answers'][$oField->id] = [
                                'question' => $oField->label,
                                'answer'   => null,
                            ];

                            /**
                             * If the field supports options then we need to find the appropriate fields
                             */
                            if ($oFieldType::SUPPORTS_OPTIONS) {

                                /**
                                 * Cast the response to an array so that fields which accept multiple values
                                 * (e.g checkboxes) validate in the same way.
                                 */
                                $aAnswer                                 = (array) $mAnswer;
                                $aData['answers'][$oField->id]['answer'] = [];

                                foreach ($aAnswer as $sAnswer) {
                                    foreach ($oField->options->data as $oOption) {
                                        if ($oOption->id == $sAnswer) {
                                            $aData['answers'][$oField->id]['answer'][] = $oOption->label;
                                            break;
                                        }
                                    }
                                }

                            } else {
                                $aData['answers'][$oField->id]['answer'] = $oFieldType->validate($mAnswer, $oField);
                            }

                        } catch (\Exception $e) {
                            $oField->error = $e->getMessage();
                            $bIsFormValid  = false;
                        }
                    }
                }

                if (!$bIsFormValid) {
                    throw new ValidationException(lang('fv_there_were_errors'));
                }

                //  Encode the answers into a string
                $aData['answers'] = json_encode(array_values($aData['answers']));
                $oResponseModel   = Factory::model('Response', 'nails/module-custom-forms');

                if (!$oResponseModel->create($aData)) {
                    throw new \Nails\Common\Exception\NailsException(
                        'Failed to save your responses. ' . $oResponseModel->lastError()
                    );
                }

                //  Send notification email?
                if (!empty($oForm->notification_email)) {
                    $oEmailer = Factory::service('Emailer', Email\Constants::MODULE_SLUG);
                    foreach ($oForm->notification_email as $sEmail) {
                        $oEmailer->send((object) [
                            'to_email' => $sEmail,
                            'type'     => 'custom_form_submitted',
                            'data'     => (object) [
                                'label'   => $oForm->label,
                                'answers' => json_decode($aData['answers']),
                            ],
                        ]);
                    }
                }

                //  Send thank you email?
                $sSubject = $oForm->thankyou_email->subject;
                $sBody    = $oForm->thankyou_email->body;

                if (isLoggedIn() && $oForm->thankyou_email->send && !empty($sSubject) && !empty($sBody)) {
                    $oEmailer = Factory::service('Emailer', Email\Constants::MODULE_SLUG);
                    $oEmailer->send((object) [
                        'to_id' => activeUser('id'),
                        'type'  => 'custom_form_submitted_thanks',
                        'data'  => (object) [
                            'subject' => $sSubject,
                            'body'    => $sBody,
                        ],
                    ]);
                }

                //  Show the thanks page
                return Factory::service('View')
                    ->setData([
                        'oForm' => $oForm,
                    ])
                    ->load([
                        $sHeaderView,
                        'forms/thanks',
                        $sFooterView,
                    ]);

            } catch (\Exception $e) {
                $this->data['error'] = $e->getMessage();
            }
        }

        Factory::service('View')
            ->setData([
                'oForm'             => $oForm,
                'bIsCaptchaEnabled' => $bIsCaptchaEnabled,
            ])
            ->load([
                $sHeaderView,
                'forms/form',
                $sFooterView,
            ]);
    }
}
