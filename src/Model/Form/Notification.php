<?php

/**
 * Manage form notifications
 *
 * @package     Nails
 * @subpackage  module-custom-form
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Model\Form;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;
use Nails\CustomForms\Constants;
use Nails\Factory;
use Nails\FormBuilder\Model\Form\Field;

/**
 * Class Notification
 *
 * @package Nails\CustomForms\Model
 */
class Notification extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'custom_form_notification';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'FormNotification';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    /**
     * The various operators
     */
    const OPERATOR_IS           = 'IS';
    const OPERATOR_IS_NOT       = 'IS_NOT';
    const OPERATOR_GREATER_THAN = 'GREATER_THAN';
    const OPERATOR_LESS_THAN    = 'LESS_THAN';
    const OPERATOR_CONTAINS     = 'CONTAINS';

    // --------------------------------------------------------------------------

    /**
     * Returns the various operators
     *
     * @return string[]
     */
    public function getOperators(): array
    {
        return [
            static::OPERATOR_IS           => 'Is',
            static::OPERATOR_IS_NOT       => 'Is not',
            static::OPERATOR_GREATER_THAN => 'Greater than',
            static::OPERATOR_LESS_THAN    => 'Less than',
            static::OPERATOR_CONTAINS     => 'Contains',
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Creates a new item, taking into consideration remapping items to non-existant fields
     *
     * @param array $aData
     * @param bool  $bReturnObject
     *
     * @return mixed|null
     * @throws FactoryException
     * @throws ModelException
     */
    public function create(array $aData = [], $bReturnObject = false)
    {
        if (
            array_key_exists('condition_field_id', $aData) &&
            preg_match('/^(\d+):(\d+)$/', (string) $aData['condition_field_id'], $aMatches)
        ) {
            //  This field needs mapped to a [newly created] field item
            /** @var Field $oFormFieldModel */
            $oFormFieldModel = Factory::model('FormField', \Nails\FormBuilder\Constants::MODULE_SLUG);
            /** @var \Nails\FormBuilder\Resource\Form\Field[] $aFields */
            $aFields = $oFormFieldModel->getAll([
                'where' => [
                    ['form_id', $aMatches[1]],
                    ['order', $aMatches[2]],
                ],
            ]);

            if (count($aFields) !== 1) {
                $this->setError('Notification field map returned more than one result.');
                return null;
            }

            $aData['condition_field_id'] = reset($aFields)->id;
        }

        return parent::create($aData, $bReturnObject);
    }
}
