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

use Nails\Factory;
use Nails\Common\Model\Base;

class Response extends Base
{
    private $oDb;
    private $tableAnswer;
    private $tableAnswerPrefix;

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();

        $this->oDb               = Factory::service('Database');
        $this->table             = NAILS_DB_PREFIX . 'custom_form_response';
        $this->tablePrefix       = 'cfr';
        $this->destructiveDelete = false;
        $this->defaultSortColumn = 'created';
        $this->defaultSortOrder  = 'desc';
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param  object $oObj      A reference to the object being formatted.
     * @param  array  $aData     The same data array which is passed to _getcount_common, for reference if needed
     * @param  array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param  array  $aBools    Fields which should be cast as booleans if not null
     * @param  array  $aFloats   Fields which should be cast as floats if not null
     * @return void
     */
    protected function formatObject(
        &$oObj,
        $aData = array(),
        $aIntegers = array(),
        $aBools = array(),
        $aFloats = array()
    ) {
        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        $oObj->answers = json_decode($oObj->answers);
    }
}
