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
use Nails\Cms\Exception\RenderException;

class Forms extends NAILS_Controller
{
    public function index()
    {
        $iFormId = (int) $this->uri->rsegment(3);

        if (empty($iFormId)) {
            show_404();
        }

        $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');
        $oForm      = $oFormModel->getById($iFormId);

        if (!empty($oForm)) {

            $this->data['oForm'] = $oForm;

            if ($this->input->post()) {

                //  Validate
                $oFormValidation = Factory::service('FormValidation');
                $aRules          = array();

                foreach ($oForm->fields as $oField) {

                    $aTemp   = array();
                    $aTemp[] = 'xss_clean';

                    if ($oField->is_required) {
                        $aTemp[] = 'required';
                    }

                    switch ($oField->type) {

                        case 'EMAIL':
                            $aTemp[] = 'valid_email';
                            break;

                        case 'NUMBER':
                            $aTemp[] = 'is_numeric';
                            break;

                        case 'URL':
                            $aTemp[] = 'prep_url';
                            break;

                        case 'DATE':
                            $aTemp[] = 'valid_date';
                            break;

                        case 'TIME':
                            $aTemp[] = 'valid_time';
                            break;

                        case 'DATETIME':
                            $aTemp[] = 'valid_datetime';
                            break;
                    }

                    $aRules['field[' . $oField->id . ']'] = implode('|', $aTemp);
                }

                foreach ($aRules as $sField => $sRules) {
                    $oFormValidation->set_rules($sField, '', $sRules);
                }

                //  Set all the messages
                $oFormValidation->set_message('required', lang('fv_required'));

                if ($oFormValidation->run()) {

                    //  Save the response
                    //  @todo

                    //  Show the thanks page
                    $this->load->view('structure/header', $this->data);
                    $this->load->view('forms/thanks', $this->data);
                    $this->load->view('structure/footer', $this->data);
                    return;

                } else {

                    $this->data['error'] = lang('fv_there_were_errors');
                }
            }

            $this->load->view('structure/header', $this->data);
            $this->load->view('forms/form', $this->data);
            $this->load->view('structure/footer', $this->data);

        } else {

            show_404();
        }
    }
}
