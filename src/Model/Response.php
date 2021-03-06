<?php

/**
 * Manage Custom form responses
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Model;

use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;
use Nails\CustomForms\Constants;

/**
 * Class Response
 *
 * @package Nails\CustomForms\Model
 */
class Response extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'custom_form_response';

    /**
     * Whether this model uses destructive delete or not
     *
     * @var bool
     */
    const DESTRUCTIVE_DELETE = false;

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'Response';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    /**
     * The default column to sort on
     *
     * @var string|null
     */
    const DEFAULT_SORT_COLUMN = 'created';

    /**
     * The default sort order
     *
     * @var string
     */
    const DEFAULT_SORT_ORDER = self::SORT_DESC;

    // --------------------------------------------------------------------------

    /**
     * Response constructor.
     *
     * @throws ModelException
     */
    public function __construct()
    {
        parent::__construct();
        $this
            ->hasOne('form', 'Form', Constants::MODULE_SLUG);
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param object $oObj      A reference to the object being formatted.
     * @param array  $aData     The same data array which is passed to _getcount_common, for reference if needed
     * @param array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param array  $aBools    Fields which should be cast as booleans if not null
     * @param array  $aFloats   Fields which should be cast as floats if not null
     *
     * @return void
     */
    protected function formatObject(
        &$oObj,
        array $aData = [],
        array $aIntegers = [],
        array $aBools = [],
        array $aFloats = []
    ) {
        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);
        $oObj->answers = json_decode($oObj->answers);
    }
}
