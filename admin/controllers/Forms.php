<?php

/**
 * Manage Custom forms
 *
 * @package     module-custom-forms
 * @subpackage  Admin
 * @category    AdminController
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Admin\Forms;

use Nails\Admin\Factory\Nav;
use Nails\Admin\Helper;
use Nails\Captcha;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Service\FormValidation;
use Nails\Common\Service\Input;
use Nails\Common\Service\Session;
use Nails\Common\Service\Uri;
use Nails\CustomForms\Controller\BaseAdmin;
use Nails\CustomForms\Model\Form;
use Nails\CustomForms\Model\Response;
use Nails\Factory;

/**
 * Class Forms
 *
 * @package Nails\Admin\Forms
 */
class Forms extends BaseAdmin
{
    /**
     * Announces this controller's navGroups
     *
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:forms:forms:browse')) {

            /** @var Nav $oNavGroup */
            $oNavGroup = Factory::factory('Nav', 'nails/module-admin');
            $oNavGroup
                ->setLabel('Custom Forms')
                ->setIcon('fa-list-alt')
                ->addAction('Browse Forms');

            return $oNavGroup;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of extra permissions for this controller
     *
     * @return array
     */
    public static function permissions(): array
    {
        $aPermissions = parent::permissions();

        $aPermissions['browse']           = 'Can browse forms';
        $aPermissions['create']           = 'Can create and duplicate forms';
        $aPermissions['edit']             = 'Can edit forms';
        $aPermissions['delete']           = 'Can delete forms';
        $aPermissions['responses']        = 'Can view responses';
        $aPermissions['responses_delete'] = 'Can delete responses';

        return $aPermissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Browse existing form
     *
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:forms:forms:browse')) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', 'nails/module-custom-forms');

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = 'Browse Forms';

        // --------------------------------------------------------------------------

        //  Get pagination and search/sort variables
        $sTableAlias = $oFormModel->getTableAlias();
        $iPage       = (int) $oInput->get('page') ? $oInput->get('page') : 0;
        $iPerPage    = (int) $oInput->get('perPage') ? $oInput->get('perPage') : 50;
        $sSortOn     = $oInput->get('sortOn') ? $oInput->get('sortOn') : $sTableAlias . '.label';
        $sSortOrder  = $oInput->get('sortOrder') ? $oInput->get('sortOrder') : 'asc';
        $sKeywords   = $oInput->get('keywords') ? $oInput->get('keywords') : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = [
            $sTableAlias . '.id'       => 'Form ID',
            $sTableAlias . '.label'    => 'Label',
            $sTableAlias . '.modified' => 'Modified Date',
        ];

        // --------------------------------------------------------------------------

        //  Define the $aData variable for the queries
        $aData = [
            'sort'     => [
                [$sSortOn, $sSortOrder],
            ],
            'keywords' => $sKeywords,
            'expand'   => ['responses'],
        ];

        //  Get the items for the page
        $totalRows           = $oFormModel->countAll($aData);
        $this->data['forms'] = $oFormModel->getAll($iPage, $iPerPage, $aData);

        //  Set Search and Pagination objects for the view
        $this->data['search']     = Helper::searchObject(true, $sortColumns, $sSortOn, $sSortOrder, $iPerPage, $sKeywords);
        $this->data['pagination'] = Helper::paginationObject($iPage, $iPerPage, $totalRows);

        //  Add a header button
        if (userHasPermission('admin:forms:forms:create')) {
            Helper::addHeaderButton('admin/forms/forms/create', 'Create Form');
        }

        // --------------------------------------------------------------------------

        Helper::loadView('index');
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new Form
     *
     * @return void
     */
    public function create()
    {
        if (!userHasPermission('admin:forms:forms:create')) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        if ($oInput->post()) {
            if ($this->runFormValidation()) {

                /** @var Form $oFormModel */
                $oFormModel = Factory::model('Form', 'nails/module-custom-forms');

                if ($oFormModel->create($this->getPostObject())) {

                    /** @var Session $oSession */
                    $oSession = Factory::service('Session');
                    $oSession->setFlashData('success', 'Form created successfully.');
                    redirect('admin/forms/forms');

                } else {
                    $this->data['error'] = 'Failed to create form.' . $oFormModel->lastError();
                }

            } else {
                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        $this->data['page']->title = 'Create Form';
        $this->loadViewData();
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Edit an existing Form
     *
     * @return void
     */
    public function edit()
    {
        if (!userHasPermission('admin:forms:forms:edit')) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', 'nails/module-custom-forms');

        $iFormId            = (int) $oUri->segment(5);
        $this->data['form'] = $oFormModel->getById(
            $iFormId,
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

        if (empty($this->data['form'])) {
            show404();
        }

        if ($oInput->post()) {
            if ($this->runFormValidation()) {
                if ($oFormModel->update($iFormId, $this->getPostObject())) {

                    /** @var Session $oSession */
                    $oSession = Factory::service('Session');
                    $oSession->setFlashData('success', 'Form updated successfully.');
                    redirect('admin/forms/forms');

                } else {
                    $this->data['error'] = 'Failed to update form. ' . $oFormModel->lastError();
                }

            } else {
                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        $this->data['page']->title = 'Edit Form';
        $this->loadViewData();
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Loads the edit view data
     *
     * @throws FactoryException
     */
    protected function loadViewData()
    {
        Factory::helper('formbuilder', 'nails/module-form-builder');
        adminLoadFormBuilderAssets('#custom-form-fields');

        /** @var Captcha\Service\Captcha $oCaptcha */
        $oCaptcha = Factory::service('Captcha', Captcha\Constants::MODULE_SLUG);

        $this->data['bIsCaptchaEnabled'] = $oCaptcha->isEnabled();

        /** @var Form\Notification $oFormNotificationModel */
        $oFormNotificationModel               = Factory::model('FormNotification', 'nails/module-custom-forms');
        $this->data['aNotificationOperators'] = $oFormNotificationModel->getOperators();

        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        $this->data['aNotifications'] = [];

        if ($oInput->post()) {
            $this->data['aNotifications'] = $this->extractNotificationsFromPost();
        } elseif (!empty($this->data['form'])) {
            $this->data['aNotifications'] = $this->extractNotificationsFromObject();
        }
    }

    // --------------------------------------------------------------------------

    private function extractNotificationsFromPost()
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        return array_map(function ($aItem) {
            $aItem = (array) $aItem;
            return (object) [
                'id'                 => (int) getFromArray('id', $aItem) ?: null,
                'email'              => getFromArray('email', $aItem),
                'condition_enabled'  => getFromArray('condition_enabled', $aItem),
                'condition_field_id' => getFromArray('condition_field_id', $aItem),
                'condition_operator' => getFromArray('condition_operator', $aItem),
                'condition_value'    => getFromArray('condition_value', $aItem),
            ];
        }, array_filter((array) $oInput->post('notifications')));
    }

    // --------------------------------------------------------------------------

    private function extractNotificationsFromObject()
    {
        return array_map(function ($oItem) {
            $aItem = (array) $oItem;
            return (object) [
                'id'                 => getFromArray('id', $aItem),
                'email'              => getFromArray('email', $aItem),
                'condition_enabled'  => getFromArray('condition_enabled', $aItem),
                'condition_field_id' => getFromArray('condition_field_id', $aItem),
                'condition_operator' => getFromArray('condition_operator', $aItem),
                'condition_value'    => getFromArray('condition_value', $aItem),
            ];
        }, $this->data['form']->notifications->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Form validation for edit/create
     *
     * @param array $aOverrides Any overrides for the fields; best to do this in the model's describeFields() method
     *
     * @return bool
     */
    protected function runFormValidation(array $aOverrides = [])
    {
        /** @var FormValidation $oFormValidation */
        $oFormValidation = Factory::service('FormValidation');
        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        //  Define the rules
        $aRules = [
            'label'                  => 'required',
            'header'                 => '',
            'footer'                 => '',
            'cta_label'              => '',
            'cta_attributes'         => '',
            'form_attributes'        => '',
            'is_minimal'             => '',
            'has_captcha'            => '',
            'notifications'          => '',
            'thankyou_email'         => '',
            'thankyou_email_subject' => '',
            'thankyou_email_body'    => '',
            'thankyou_page_title'    => 'required',
            'thankyou_page_body'     => '',
        ];

        foreach ($aRules as $sKey => $sRules) {
            $oFormValidation->set_rules($sKey, '', $sRules);
        }

        $oFormValidation->set_message('required', lang('fv_required'));

        $bValidForm = $oFormValidation->run();

        //  Validate fields
        Factory::helper('formbuilder', 'nails/module-form-builder');
        $bValidFields = adminValidateFormData($oInput->post('fields'));

        return $bValidForm && $bValidFields;
    }

    // --------------------------------------------------------------------------

    /**
     * Extract data from post variable
     *
     * @return array
     */
    protected function getPostObject(): array
    {
        Factory::helper('formbuilder', 'nails/module-form-builder');
        /** @var Input $oInput */
        $oInput  = Factory::service('Input');
        $iFormId = !empty($this->data['form']->form->id) ? $this->data['form']->form->id : null;
        $aData   = [
            'label'                  => $oInput->post('label'),
            'header'                 => $oInput->post('header'),
            'footer'                 => $oInput->post('footer'),
            'cta_label'              => $oInput->post('cta_label'),
            'cta_attributes'         => $oInput->post('cta_attributes'),
            'form_attributes'        => $oInput->post('form_attributes'),
            'is_minimal'             => (bool) $oInput->post('is_minimal'),
            'thankyou_email'         => (bool) $oInput->post('thankyou_email'),
            'thankyou_email_subject' => $oInput->post('thankyou_email_subject'),
            'thankyou_email_body'    => $oInput->post('thankyou_email_body'),
            'thankyou_page_title'    => $oInput->post('thankyou_page_title'),
            'thankyou_page_body'     => $oInput->post('thankyou_page_body'),
            'form'                   => adminNormalizeFormData(
                $iFormId,
                $oInput->post('has_captcha'),
                $oInput->post('fields')
            ),
            'notifications'          => $this->extractNotificationsFromPost(),
        ];

        /**
         * For fieldNumber:{\d} values we need to generate a signature so we can
         * fetch the item later as the notificatuons require an Id, and this isn't
         * available yet
         */

        foreach ($aData['notifications'] as $aNotification) {
            if (preg_match('/^fieldNumber:(\d)+$/', $aNotification->condition_field_id, $aMatches)) {
                $aOption                           = getFromArray(
                    $aMatches[1],
                    $aData['form']['fields']
                );
                $aNotification->condition_field_id = $aOption['form_id'] . ':' . $aOption['order'];
            }
        }

        return $aData;
    }

    // --------------------------------------------------------------------------

    /**
     * Delete an existing form
     *
     * @return void
     */
    public function delete()
    {
        if (!userHasPermission('admin:forms:forms:delete')) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', 'nails/module-custom-forms');

        $iFormId = (int) $oUri->segment(5);
        $sReturn = $oInput->get('return') ? $oInput->get('return') : 'admin/forms/forms/index';

        if ($oFormModel->delete($iFormId)) {
            $sStatus  = 'success';
            $sMessage = 'Custom form was deleted successfully.';
        } else {
            $sStatus  = 'error';
            $sMessage = 'Custom form failed to delete. ' . $oFormModel->lastError();
        }

        /** @var Session $oSession */
        $oSession = Factory::service('Session');
        $oSession->setFlashData($sStatus, $sMessage);

        redirect($sReturn);
    }

    // --------------------------------------------------------------------------

    /**
     * Show responses to a form
     *
     * @throws FactoryException
     * @throws ModelException
     */
    public function responses()
    {
        if (!userHasPermission('admin:forms:forms:responses')) {
            unauthorised();
        }

        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', 'nails/module-custom-forms');

        $iFormId = (int) $oUri->segment(5);
        $oForm   = $oFormModel->getById($iFormId, ['expand' => ['responses']]);

        if (empty($oForm)) {
            show404();
        }

        $iResponseId     = (int) $oUri->segment(6);
        $sResponseMethod = $oUri->segment(7) ?: 'view';

        if (empty($iResponseId)) {

            $this->responsesList($oForm);

        } else {

            /** @var Response $oResponseModel */
            $oResponseModel = Factory::model('Response', 'nails/module-custom-forms');
            $oResponse      = $oResponseModel->getById($iResponseId);

            if (empty($oResponse)) {
                show404();
            }

            switch ($sResponseMethod) {

                case 'delete':
                    $this->responseDelete($oResponse, $oForm);
                    break;

                case 'view':
                default:
                    $this->responseView($oResponse, $oForm);
                    break;
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * List the responses
     *
     * @param $oForm
     *
     * @throws FactoryException
     * @throws ModelException
     */
    protected function responsesList($oForm)
    {
        /** @var Response $oResponseModel */
        $oResponseModel = Factory::model('Response', 'nails/module-custom-forms');

        $this->data['page']->title = 'Responses for form: ' . $oForm->label;
        $this->data['form']        = $oForm;
        $this->data['responses']   = $oResponseModel->getAll([
            'where' => [
                ['form_id', $oForm->id],
            ],
        ]);

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        if ($oInput->get('dl')) {

            $aResults = [];
            $aColumns = ['Created'];
            $oHeader  = reset($this->data['responses']);

            foreach ($oHeader->answers as $oColumn) {
                $aColumns[] = $oColumn->question;
            }

            foreach ($this->data['responses'] as $oResponse) {
                $aRow = [$oResponse->created];
                foreach ($oResponse->answers as $oColumn) {
                    $aRow[] = is_array($oColumn->answer) ? implode('|', $oColumn->answer) : $oColumn->answer;
                }

                $aResult[] = array_combine($aColumns, $aRow);
            }

            Helper::loadCsv($aResult, $this->data['form']->slug . '.csv');

        } else {
            Helper::addHeaderButton(uri_string() . '?dl=1', 'Download as CSV');
            Helper::loadView('responses');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * View a single response
     *
     * @param $oResponse
     * @param $oForm
     *
     * @throws FactoryException
     */
    protected function responseView($oResponse, $oForm)
    {
        $this->data['page']->title = 'Responses for form: ' . $oForm->label;
        $this->data['response']    = $oResponse;
        Helper::loadView('response');
    }

    // --------------------------------------------------------------------------

    /**
     * Delete a response
     *
     * @param $oResponse
     * @param $oForm
     *
     * @throws FactoryException
     * @throws ModelException
     */
    protected function responseDelete($oResponse, $oForm)
    {
        /** @var Session $oSession */
        $oSession = Factory::service('Session');
        /** @var Response $oModel */
        $oModel = Factory::model('Response', 'nails/module-custom-forms');

        if ($oModel->delete($oResponse->id)) {
            $oSession->setFlashData('success', 'Response deleted successfully!');
        } else {
            $oSession->setFlashData('error', 'Failed to delete response. ' . $oModel->lastError());
        }

        redirect('admin/forms/forms/responses/' . $oForm->id);
    }

    // --------------------------------------------------------------------------

    /**
     * Creates a copy of a form
     *
     * @throws FactoryException
     * @throws ModelException
     */
    public function copy()
    {
        if (!userHasPermission('admin:forms:forms:create')) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', 'nails/module-custom-forms');

        try {

            $iNewFormId   = $oFormModel->copy((int) $oUri->segment(5));
            $sStatus      = 'success';
            $sMessage     = 'Custom form was copied successfully.';
            $sRedirectUrl = 'admin/forms/forms/edit/' . $iNewFormId;

        } catch (\Exception $e) {
            $sStatus      = 'error';
            $sMessage     = 'Custom form failed to copy. ' . $e->getMessage();
            $sRedirectUrl = $oInput->get('return') ? $oInput->get('return') : 'admin/forms/forms/index';
        }

        /** @var Session $oSession */
        $oSession = Factory::service('Session');
        $oSession->setFlashData($sStatus, $sMessage);

        redirect($sRedirectUrl);
    }
}
