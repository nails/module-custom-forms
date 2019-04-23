<?php

namespace Nails\CustomForms\DataExport\Source;

use Nails\Admin\Exception\DataExport\FailureException;
use Nails\Admin\Interfaces\DataExport\Source;
use Nails\Factory;

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
        $oModel = Factory::model('Form', 'nails/module-custom-forms');
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
        $oFormModel     = Factory::model('Form', 'nails/module-custom-forms');
        $oResponseModel = Factory::model('Response', 'nails/module-custom-forms');

        $iFormId = (int) getFromArray('form_id', $aOptions) ?: null;
        $oForm   = $oFormModel->getById($iFormId);
        if (empty($oForm)) {
            throw new FailureException('Invalid Form ID');
        }

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

        return Factory::factory('DataExportSourceResponse', 'nails/module-admin')
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
