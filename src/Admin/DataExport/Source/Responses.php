<?php

namespace Nails\CustomForms\Admin\DataExport\Source;

use Nails\Common\Helper\ArrayHelper;
use Nails\Admin\Exception\DataExport\FailureException;
use Nails\Admin\Interfaces\DataExport\Source;
use Nails\CustomForms\Constants;
use Nails\CustomForms\Resource;
use Nails\Factory;

/**
 * Class Responses
 *
 * @package Nails\CustomForms\Admin\DataExport\Source
 */
class Responses implements Source
{
    public function getLabel(): string
    {
        return 'Custom Forms - Responses';
    }

    // --------------------------------------------------------------------------

    public function getFileName(): string
    {
        return 'custom-forms-responses';
    }

    // --------------------------------------------------------------------------

    public function getDescription(): string
    {
        return 'Exports all responses to a particular form';
    }

    // --------------------------------------------------------------------------

    public function getOptions(): array
    {
        $oModel = Factory::model('Form', Constants::MODULE_SLUG);
        return [
            [
                'key'     => 'form_id',
                'label'   => 'Form',
                'type'    => 'dropdown',
                'class'   => 'select2',
                'options' => $oModel->getAllFlat(),
            ],
        ];
    }

    // --------------------------------------------------------------------------

    public function isEnabled(): bool
    {
        return true;
    }

    // --------------------------------------------------------------------------

    public function execute($aOptions = [])
    {
        $oFormModel     = Factory::model('Form', Constants::MODULE_SLUG);
        $oResponseModel = Factory::model('Response', Constants::MODULE_SLUG);

        $iFormId = (int) ArrayHelper::get('form_id', $aOptions) ?: null;
        /** @var Resource\Form|null $oForm */
        $oForm = $oFormModel->getById($iFormId);
        if (empty($oForm)) {
            throw new FailureException('Invalid Form ID');
        }

        /** @var Resource\Response[] $aResults */
        $aResults = $oResponseModel->getAll([
            'expand' => ['form'],
            'where'  => [
                ['form_id', $oForm->id],
            ],
        ]);

        $aQuestions        = [];
        $aFields           = [];
        $aFormattedResults = [];
        foreach ($aResults as $oResult) {
            foreach ($oResult->answers as $oAnswer) {
                $aQuestions[md5($oAnswer->question)] = '[Did Not Answer]';
                $aFields[md5($oAnswer->question)]    = $oAnswer->question;
            }
        }

        foreach ($aResults as $oResult) {
            $aTemp = array_merge([
                'id'         => $oResult->id,
                'form_id'    => $oResult->form->id,
                'form_label' => $oResult->form->label,
            ], $aQuestions);

            foreach ($oResult->answers as $oAnswer) {
                $aTemp[md5($oAnswer->question)] = is_array($oAnswer->answer) ? implode(', ', $oAnswer->answer) : $oAnswer->answer;
            }

            $aFormattedResults[] = array_values($aTemp);
        }

        return Factory::factory('DataExportSourceResponse', \Nails\Admin\Constants::MODULE_SLUG)
            ->setLabel($this->getLabel())
            ->setFileName($this->getFileName())
            ->setFields(array_values(array_merge([
                'id'         => 'Response ID',
                'form_id'    => 'Form ID',
                'form_label' => 'Form Label',
            ], $aFields)))
            ->setData($aFormattedResults);
    }
}
