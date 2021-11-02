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
use Nails\Captcha;
use Nails\Common\Exception\ValidationException;
use Nails\CustomForms\Constants;
use Nails\Email;
use Nails\Factory;

/**
 * Class Forms
 */
class Forms extends Base
{
    public function index()
    {
        /** @var \Nails\Common\Service\Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var \Nails\Common\Service\Input $oInput */
        $oInput = Factory::service('Input');
        /** @var \Nails\CustomForms\Model\Form $oFormModel */
        $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);
        /** @var \Nails\FormBuilder\Service\FieldType $oFieldTypeService */
        $oFieldTypeService = Factory::service('FieldType', \Nails\FormBuilder\Constants::MODULE_SLUG);
        /** @var Captcha\Service\Captcha $oCaptcha */
        $oCaptcha = Factory::service('Captcha', Captcha\Constants::MODULE_SLUG);

        Factory::helper('formbuilder', \Nails\FormBuilder\Constants::MODULE_SLUG);

        $sFormSlug         = $oUri->rsegment(3);
        $bIsCaptchaEnabled = $oCaptcha->isEnabled();
        $oForm             = $oFormModel->getBySlug(
            $sFormSlug,
            [
                'expand' => [
                    'notifications',
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

                if (!formBuilderValidate($oForm->form->fields->data, (array) $oInput->post('field'))) {
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
                                'field'    => (object) [
                                    'id'   => $oField->id,
                                    'type' => get_class($oFieldType),
                                ],
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
                /** @var \Nails\CustomForms\Model\Response $oResponseModel */
                $oResponseModel = Factory::model('Response', Constants::MODULE_SLUG);

                $oResponse = $oResponseModel->create($aData, true);
                if (empty($oResponse)) {
                    throw new \Nails\Common\Exception\NailsException(
                        'Failed to save your responses. ' . $oResponseModel->lastError()
                    );
                }

                //  Send notification email?
                if (!empty($oForm->notifications->data)) {

                    /** @var \Nails\CustomForms\Factory\Email\Form\Submitted $oEmail */
                    $oEmail = Factory::factory('EmailFormSubmitted', Constants::MODULE_SLUG);
                    $oEmail
                        ->data([
                            'label'   => $oForm->label,
                            'answers' => json_decode($aData['answers']),
                        ]);


                    foreach ($oForm->notifications->data as $oNotify) {
                        if ($this->doSendNotification($oNotify, $oResponse)) {
                            try {
                                $oEmail
                                    ->to($oNotify->email)
                                    ->send();
                            } catch (\Nails\Email\Exception\EmailerException $e) {
                                //  Do something with this info?
                            }
                        }
                    }
                }

                //  Send thank you email?
                $sSubject = $oForm->thankyou_email->subject;
                $sBody    = $oForm->thankyou_email->body;

                if (isLoggedIn() && $oForm->thankyou_email->send && !empty($sSubject) && !empty($sBody)) {

                    /** @var \Nails\CustomForms\Factory\Email\Form\Submitted\Thanks $oEmail */
                    $oEmail = Factory::factory('EmailFormSubmittedThanks', Constants::MODULE_SLUG);

                    try {
                        $oEmail
                            ->to(activeUser())
                            ->data([
                                'subject' => $sSubject,
                                'body'    => $sBody,
                            ])
                            ->send();
                    } catch (\Nails\Email\Exception\EmailerException $e) {
                        //  Do something with this info?
                    }
                }

                $this->oMetaData->setTitles([$oForm->thankyou_page->title]);

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
                $this->oUserFeedback->error($e->getMessage());
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

    // --------------------------------------------------------------------------

    /**
     * Determines whetehr to send a notification
     *
     * @param \Nails\CustomForms\Resource\Form\Notification $oNotify
     * @param \Nails\CustomForms\Resource\Response          $oResponse
     *
     * @return bool
     * @throws \Nails\Common\Exception\FactoryException
     */
    private function doSendNotification(
        \Nails\CustomForms\Resource\Form\Notification $oNotify,
        \Nails\CustomForms\Resource\Response $oResponse
    ) {
        //  The easy ones
        if (empty($oNotify->email)) {
            return false;
        } elseif (!valid_email($oNotify->email)) {
            return false;
        } elseif (!(bool) $oNotify->condition_enabled) {
            return true;
        }

        /** @var \Nails\CustomForms\Model\Form\Notification $oModel */
        $oModel = Factory::model('FormNotification', Constants::MODULE_SLUG);

        foreach ($oResponse->answers as $oAnswer) {

            if (is_array($oAnswer->answer)) {
                $sAnswerToTest = reset($oAnswer->answer);
            } else {
                $sAnswerToTest = $oAnswer->answer;
            }

            if ($oAnswer->field->id == $oNotify->condition_field_id) {
                switch ($oNotify->condition_operator) {
                    case $oModel::OPERATOR_IS:
                        return strtolower($sAnswerToTest) == strtolower($oNotify->condition_value);
                        break;
                    case $oModel::OPERATOR_IS_NOT:
                        return strtolower($sAnswerToTest) != strtolower($oNotify->condition_value);
                        break;
                    case $oModel::OPERATOR_GREATER_THAN:
                        return $sAnswerToTest > $oNotify->condition_value;
                        break;
                    case $oModel::OPERATOR_LESS_THAN:
                        return $sAnswerToTest < $oNotify->condition_value;
                        break;
                    case $oModel::OPERATOR_CONTAINS:
                        return strpos(strtolower($sAnswerToTest), strtolower($oNotify->condition_value)) !== -1;
                        break;
                }
            }
        }

        return false;
    }
}
