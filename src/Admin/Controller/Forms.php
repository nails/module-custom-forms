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

namespace Nails\CustomForms\Admin\Controller;

use Nails\Admin\Controller\Base;
use Nails\Admin\Factory\Nav;
use Nails\Admin\Helper;
use Nails\Captcha;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Service\FormValidation;
use Nails\Common\Service\Input;
use Nails\Common\Service\Uri;
use Nails\CustomForms\Admin\Permission;
use Nails\CustomForms\Constants;
use Nails\CustomForms\Controller\BaseAdmin;
use Nails\CustomForms\Model\Form;
use Nails\CustomForms\Model\Response;
use Nails\Factory;
use Nails\FormBuilder;

/**
 * Class Forms
 *
 * @package Nails\CustomForms\Admin\Controller
 */
class Forms extends Base
{
    /**
     * Announces this controller's navGroups
     *
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission(Permission\Form\Browse::class)) {

            /** @var Nav $oNavGroup */
            $oNavGroup = Factory::factory('Nav', \Nails\Admin\Constants::MODULE_SLUG);
            $oNavGroup
                ->setLabel('Custom Forms')
                ->setIcon('fa-list-alt')
                ->addAction('Browse Forms');

            return $oNavGroup;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Browse existing form
     *
     * @return void
     */
    public function index()
    {
        if (!userHasPermission(Permission\Form\Browse::class)) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);

        // --------------------------------------------------------------------------

        //  Set method info
        $this->setTitles(['Browse Forms']);

        // --------------------------------------------------------------------------

        //  Get pagination and search/sort variables
        $sTableAlias = $oFormModel->getTableAlias();
        $iPage       = (int) $oInput->get('page') ? $oInput->get('page') : 0;
        $iPerPage    = (int) $oInput->get('perPage') ? $oInput->get('perPage') : 50;
        $sSortOn     = $oInput->get('sortOn') ? $oInput->get('sortOn') : $sTableAlias . '.modified';
        $sSortOrder  = $oInput->get('sortOrder') ? $oInput->get('sortOrder') : 'desc';
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
        if (userHasPermission(Permission\Form\Create::class)) {
            Helper::addHeaderButton(static::url('create'), 'Create Form');
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
        if (!userHasPermission(Permission\Form\Create::class)) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        if ($oInput->post()) {
            if ($this->runFormValidation()) {

                /** @var Form $oFormModel */
                $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);
                $iFormId    = $oFormModel->create($this->getPostObject());

                if ($iFormId) {

                    $this->oUserFeedback->success('Form created successfully.');
                    redirect(static::url('edit/' . $iFormId));

                } else {
                    $this->oUserFeedback->error('Failed to create form.' . $oFormModel->lastError());
                }

            } else {
                $this->oUserFeedback->error(lang('fv_there_were_errors'));
            }
        }

        // --------------------------------------------------------------------------

        $this->loadViewData();
        $this
            ->setTitles(['Create Form'])
            ->loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Edit an existing Form
     *
     * @return void
     */
    public function edit()
    {
        if (!userHasPermission(Permission\Form\Edit::class)) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);

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
            if ($this->runFormValidation([], $this->data['form'])) {
                if ($oFormModel->update($iFormId, $this->getPostObject())) {

                    $this->oUserFeedback->success('Form updated successfully.');
                    redirect(static::url('edit/' . $this->data['form']->id));

                } else {
                    $this->oUserFeedback->error('Failed to update form. ' . $oFormModel->lastError());
                }

            } else {
                $this->oUserFeedback->error(lang('fv_there_were_errors'));
            }
        }

        // --------------------------------------------------------------------------

        $this->loadViewData();
        $this
            ->setTitles(['Edit Form'])
            ->loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Loads the edit view data
     *
     * @throws FactoryException
     */
    protected function loadViewData()
    {
        /** @var Captcha\Service\Captcha $oCaptcha */
        $oCaptcha = Factory::service('Captcha', Captcha\Constants::MODULE_SLUG);

        $this->data['bIsCaptchaEnabled'] = $oCaptcha->isEnabled();

        /** @var Form\Notification $oFormNotificationModel */
        $oFormNotificationModel               = Factory::model('FormNotification', Constants::MODULE_SLUG);
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
                'condition_enabled'  => (int) getFromArray('condition_enabled', $aItem),
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
                'condition_enabled'  => (int) getFromArray('condition_enabled', $aItem),
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
    protected function runFormValidation(array $aOverrides = [], $oForm = null)
    {
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);
        /** @var FormValidation $oFormValidation */
        $oFormValidation = Factory::service('FormValidation');
        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        try {

            $oFormValidation
                ->buildValidator([
                    'label'                  => [FormValidation::RULE_REQUIRED],
                    'slug'                   => [
                        $oForm
                            ? FormValidation::rule(FormValidation::RULE_UNIQUE_IF_DIFF, $oFormModel->getTableName(), 'slug', $oForm->slug)
                            : FormValidation::rule(FormValidation::RULE_IS_UNIQUE, $oFormModel->getTableName(), 'slug'),
                    ],
                    'header'                 => [''],
                    'footer'                 => [''],
                    'cta_label'              => [''],
                    'cta_attributes'         => [''],
                    'form_attributes'        => [''],
                    'is_minimal'             => [''],
                    'has_captcha'            => [''],
                    'notifications'          => [''],
                    'thankyou_email'         => [''],
                    'thankyou_email_subject' => [''],
                    'thankyou_email_body'    => [''],
                    'thankyou_page_title'    => [
                        function ($sTitle) use ($oInput) {
                            $sTitle = trim($sTitle);
                            $mBody  = json_decode($oInput->post('thankyou_page_body'));
                            if (empty($sTitle) && empty($mBody)) {
                                throw new ValidationException(
                                    'Thank you page title is required if no body is set.'
                                );
                            }
                        },
                    ],
                    'thankyou_page_body'     => [
                        function ($mBody) use ($oInput) {
                            $sTitle = trim($oInput->post('thankyou_page_title'));
                            $mBody  = json_decode($oInput->post('thankyou_page_body'));
                            if (empty($mBody) && empty($sTitle)) {
                                throw new ValidationException(
                                    'Thank you page body is required if no title is set.'
                                );
                            }
                        },
                    ],
                ])
                ->run();

            $bValidForm = true;

        } catch (ValidationException $e) {
            $bValidForm = false;
        }

        try {

            FormBuilder\Helper\FormBuilder::adminValidateFormData(
                (array) $oInput->post('fields')
            );

            $bValidFields = true;

        } catch (ValidationException $e) {
            $bValidFields = false;
        }

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
        Factory::helper('formbuilder', FormBuilder\Constants::MODULE_SLUG);
        /** @var Input $oInput */
        $oInput  = Factory::service('Input');
        $iFormId = !empty($this->data['form']->form->id) ? $this->data['form']->form->id : null;
        $aData   = [
            'label'                  => trim($oInput->post('label')),
            'header'                 => trim($oInput->post('header')) ?: '[]',
            'footer'                 => trim($oInput->post('footer')) ?: '[]',
            'cta_label'              => $oInput->post('cta_label'),
            'cta_attributes'         => $oInput->post('cta_attributes'),
            'form_attributes'        => $oInput->post('form_attributes'),
            'is_minimal'             => (bool) $oInput->post('is_minimal'),
            'thankyou_email'         => (bool) $oInput->post('thankyou_email'),
            'thankyou_email_subject' => $oInput->post('thankyou_email_subject'),
            'thankyou_email_body'    => $oInput->post('thankyou_email_body'),
            'thankyou_page_title'    => $oInput->post('thankyou_page_title'),
            'thankyou_page_body'     => trim($oInput->post('thankyou_page_body')) ?: '[]',
            'form'                   => adminNormalizeFormData(
                $iFormId,
                $oInput->post('has_captcha'),
                $oInput->post('fields')
            ),
            'notifications'          => $this->extractNotificationsFromPost(),
        ];

        $sSlug = trim($oInput->post('slug'));
        if ($sSlug) {
            $aData['slug'] = $sSlug;
        }

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
        if (!userHasPermission(Permission\Form\Delete::class)) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);

        $iFormId = (int) $oUri->segment(5);
        $sReturn = $oInput->get('return')
            ? $oInput->get('return')
            : static::url();

        if ($oFormModel->delete($iFormId)) {
            $this->oUserFeedback->success('Custom form was deleted successfully.');
        } else {
            $this->oUserFeedback->error('Custom form failed to delete. ' . $oFormModel->lastError());
        }

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
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);

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
            $oResponseModel = Factory::model('Response', Constants::MODULE_SLUG);
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
        if (!userHasPermission(Permission\Response\Browse::class)) {
            unauthorised();
        }

        /** @var Response $oResponseModel */
        $oResponseModel = Factory::model('Response', Constants::MODULE_SLUG);

        $this->setTitles(['Responses for form: ' . $oForm->label]);

        $this->data['form']      = $oForm;
        $this->data['responses'] = $oResponseModel->getAll([
            'where' => [
                ['form_id', $oForm->id],
            ],
        ]);

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        if ($oInput->get('dl')) {

            $aResults = [];
            $aColumns = [];
            $oHeader  = reset($this->data['responses']);

            //  Extract all questions
            foreach ($this->data['responses'] as $oResponse) {
                foreach ($oResponse->answers as $oAnswer) {
                    if (!in_array($oAnswer->question, $aColumns)) {
                        $aColumns[] = $oAnswer->question;
                    }
                }
            }

            foreach ($this->data['responses'] as $oResponse) {

                $aRow = [];
                foreach ($aColumns as $sQuestion) {
                    $bFound = false;
                    foreach ($oResponse->answers as $oAnswer) {
                        if ($sQuestion === $oAnswer->question) {
                            $bFound = true;
                            $aRow[] = is_array($oAnswer->answer)
                                ? implode('|', $oAnswer->answer)
                                : $oAnswer->answer;
                            break;
                        }
                    }

                    if (!$bFound) {
                        $aRow[] = null;
                    }
                }

                $aResult[] = array_combine(
                    array_merge(['Created'], $aColumns),
                    array_merge([(string) $oResponse->created], $aRow),
                );
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
        if (!userHasPermission(Permission\Response\View::class)) {
            unauthorised();
        }

        $this
            ->setData('response', $oResponse)
            ->setTitles(['Responses for form: ' . $oForm->label])
            ->loadView('response');
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
        if (!userHasPermission(Permission\Response\Delete::class)) {
            unauthorised();
        }

        /** @var Response $oModel */
        $oModel = Factory::model('Response', Constants::MODULE_SLUG);

        if ($oModel->delete($oResponse->id)) {
            $this->oUserFeedback->success('Response deleted successfully!');
        } else {
            $this->oUserFeedback->error('Failed to delete response. ' . $oModel->lastError());
        }

        redirect(static::url('responses/' . $oForm->id));
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
        if (!userHasPermission(Permission\Form\Create::class)) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Form $oFormModel */
        $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);

        try {

            $iNewFormId = $oFormModel->copy((int) $oUri->segment(5));
            $this->oUserFeedback->success('Custom form was copied successfully.');
            $sRedirectUrl = static::url('edit/' . $iNewFormId);

        } catch (\Exception $e) {
            $this->oUserFeedback->error('Custom form failed to copy. ' . $e->getMessage());
            $sRedirectUrl = $oInput->get('return')
                ? $oInput->get('return')
                : static::url();
        }

        redirect($sRedirectUrl);
    }
}
