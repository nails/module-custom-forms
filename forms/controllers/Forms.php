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

use Nails\Factory;
use App\Controller\Base;
use Nails\Cms\Exception\RenderException;

class Forms extends Base
{
    public function index()
    {
        $sFormSlug = $this->uri->rsegment(3);
        if (empty($sFormSlug)) {
            show_404();
        }

        $oFormModel         = Factory::model('Form', 'nailsapp/module-custom-forms');
        $oFormFieldModel    = Factory::model('FormField', 'nailsapp/module-form-builder');
        $oFieldTypeModel    = Factory::model('FieldType', 'nailsapp/module-form-builder');
        $oDefaultValueModel = Factory::model('FieldType', 'nailsapp/module-form-builder');
        $oCaptcha           = Factory::service('Captcha', 'nailsapp/module-captcha');

        Factory::helper('formbuilder', 'nailsapp/module-form-builder');

        $oForm = $oFormModel->getBySlug($sFormSlug, array('includeForm' => true));

        if (!empty($oForm)) {

            $this->data['oForm']             = $oForm;
            $this->data['bIsCaptchaEnabled'] = $oCaptcha->isEnabled();

            if ($oForm->is_minimal) {
                $this->data['headerOverride'] = 'structure/header/blank';
                $this->data['footerOverride'] = 'structure/footer/blank';
            }

            if ($this->input->post()) {

                $bisFormValid = formBuilderValidate(
                    $oForm->form->fields->data,
                    $this->input->post('field')
                );

                if ($oForm->form->has_captcha && $this->data['bIsCaptchaEnabled']) {

                    if (!$oCaptcha->verify()) {
                        $bIsCaptchaValid            = false;
                        $this->data['captchaError'] = 'You failed the captcha test.';
                    }

                } else {

                    $bIsCaptchaValid = true;
                }

                if ($bisFormValid && $bIsCaptchaValid) {

                    //  Save the response
                    $aData = array(
                        'form_id' => $oForm->id,
                        'answers' => array()
                    );

                    /**
                     * Build the answer array; this should contain the text equivilents of all fields so that
                     * should the parent form change, the answers won't be affected
                     */
                    foreach ($oForm->form->fields->data as &$oField) {

                        $oFieldType = $oFieldTypeModel->getBySlug($oField->type);
                        if (!empty($oFieldType)) {

                            try {

                                $mAnswer = !empty($_POST['field'][$oField->id]) ? $_POST['field'][$oField->id] : null;

                                $aData['answers'][$oField->id] = array(
                                    'question' => $oField->label,
                                    'answer'   => null
                                );

                                /**
                                 * If the field supports options then we need to find the appropriate fields
                                 */

                                if ($oFieldType::SUPPORTS_OPTIONS) {

                                    /**
                                     * Cast the response to an array so that fields which accept multiple values
                                     * (e.g checkboxes) validate in the same way.
                                     */

                                    $aAnswer = (array) $mAnswer;

                                    $aData['answers'][$oField->id]['answer'] = array();

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
                                $bisFormValid  = false;
                            }
                        }
                    }

                    if ($bisFormValid) {

                        //  Encode the answers into a string
                        $aData['answers'] = json_encode(array_values($aData['answers']));
                        $oResponseModel   = Factory::model('Response', 'nailsapp/module-custom-forms');

                        if ($oResponseModel->create($aData)) {

                            //  Send notification email?
                            if (!empty($oForm->notification_email)) {

                                foreach ($oForm->notification_email as $sEmail) {

                                    $oEmail                = new \stdClass();
                                    $oEmail->to_email      = $sEmail;
                                    $oEmail->type          = 'custom_form_submitted';
                                    $oEmail->data          = new \stdClass();
                                    $oEmail->data->label   = $oForm->label;
                                    $oEmail->data->answers = json_decode($aData['answers']);

                                    $this->emailer->send($oEmail);
                                }
                            }

                            //  Send thank you email?
                            $sSubject = $oForm->thankyou_email->subject;
                            $sBody    = $oForm->thankyou_email->body;

                            if (isLoggedIn() && $oForm->thankyou_email->send && !empty($sSubject) && !empty($sBody)) {

                                    $oEmail                = new \stdClass();
                                    $oEmail->to_id         = activeUser('id');
                                    $oEmail->type          = 'custom_form_submitted_thanks';
                                    $oEmail->data          = new \stdClass();
                                    $oEmail->data->subject = $sSubject;
                                    $oEmail->data->body    = $sBody;

                                    $this->emailer->send($oEmail);
                            }

                            $this->data['oForm'] = $oForm;

                            //  Show the thanks page
                            $oView = Factory::service('View');
                            $oView->load('structure/header', $this->data);
                            $oView->load('forms/thanks', $this->data);
                            $oView->load('structure/footer', $this->data);
                            return;

                        } else {

                            $this->data['error'] = 'Failed to save your responses. ' . $oResponseModel->lastError();
                        }

                    } else {

                        $this->data['error'] = lang('fv_there_were_errors');
                    }

                } else {

                    $this->data['error'] = lang('fv_there_were_errors');
                }
            }

            $oView = Factory::service('View');
            $oView->load('structure/header', $this->data);
            $oView->load('forms/form', $this->data);
            $oView->load('structure/footer', $this->data);

        } else {

            show_404();
        }
    }
}
