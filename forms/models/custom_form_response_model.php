<?php

class Custom_form_response_model extends NAILS_Model
{
    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();

        $this->table             = NAILS_DB_PREFIX . 'custom_form_response';
        $this->tablePrefix       = 'fr';
        $this->tableAnswer       = NAILS_DB_PREFIX . 'custom_form_response_answer';
        $this->tableAnswerPrefix = 'fra';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all response objects
     * @param null $page The page to return
     * @param null $perPage The number of objects per page
     * @param array $data Data to pass to _getcount_common
     * @param bool|false $includeDeleted Whether to include deleted results
     * @param string $_caller Internal flag of calling method
     * @return array
     */
    public function get_all(
        $page = null,
        $perPage = null,
        $data = array(),
        $includeDeleted = false,
        $_caller = 'GET_ALL'
    ) {

        $aResponses = parent::get_all($page, $perPage, $data, $includeDeleted, $_caller);

        if (!empty($aResponses)) {
            if (!empty($data['include_answers'])) {
                foreach ($aResponses as $oResponse) {
                    $oResponse->questions = $this->getAnswersForResponse($oResponse->id);
                }
            }
        }

        return $aResponses;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a response by its ID
     * @param int $iId The Id of the response to return
     * @param array $aData Data to pass to _getcount_common
     * @return mixed
     */
    public function get_by_id($iId, $aData = array())
    {
        $aData['include_answers'] = true;
        return parent::get_by_id($iId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     * @param  array  $data    Data passed from the calling method
     * @param  string $_caller The name of the calling method
     * @return void
     **/
    protected function _getcount_common($aData = array(), $_caller = null)
    {
        if (empty($aData['sort'])) {
            $aData['sort'] = array(
                array($this->tablePrefix . '.created', 'DESC')
            );
        }
        parent::_getcount_common($aData, $_caller);
    }

    // --------------------------------------------------------------------------

    /**
    * Returns all answers associated with a particular response
    * @param int $iFormId The ID of the form
    * @return mixed
    */
    private function getAnswersForResponse($iResponseId)
    {
        $this->db->select('fra.id,fra.form_field_id,ff.label field_label,fra.value text_value');
        $this->db->select('fra.form_field_option_id,ffo.label option_label, ff.order field_order');
        $this->db->join(
            NAILS_DB_PREFIX . 'custom_form_field ff',
            $this->tableAnswerPrefix . '.form_field_id = ff.id',
            'LEFT'
        );
        $this->db->join(
            NAILS_DB_PREFIX . 'custom_form_field_option ffo',
            $this->tableAnswerPrefix . '.form_field_option_id = ffo.id',
            'LEFT'
        );
        $this->db->where($this->tableAnswerPrefix . '.form_response_id', $iResponseId);
        $this->db->order_by('ff.order,ff.id');
        $aAnswers = $this->db->get($this->tableAnswer . ' ' . $this->tableAnswerPrefix)->result();
        $aOut = array();

        foreach ($aAnswers as $oAnswer) {

            if (empty($aOut[$oAnswer->form_field_id])) {

                $aOut[$oAnswer->form_field_id] = new \stdClass();
                $aOut[$oAnswer->form_field_id]->number   = $oAnswer->field_order + 1;
                $aOut[$oAnswer->form_field_id]->question = $oAnswer->field_label;
                $aOut[$oAnswer->form_field_id]->answer   = array();
            }

            if (!empty($oAnswer->text_value)) {

                $aOut[$oAnswer->form_field_id]->answer[] = $oAnswer->text_value;

            } elseif (!empty($oAnswer->option_label)) {

                $aOut[$oAnswer->form_field_id]->answer[] = $oAnswer->option_label;

            } else {

                $aOut[$oAnswer->form_field_id]->answer[] = 'Did not answer';
            }
        }

        $aOut = array_values($aOut);

        foreach ($aOut as $oAnswer) {

            if (count($oAnswer->answer) <= 1) {

                $oAnswer->answer = trim(implode($oAnswer->answer));
            }
        }

        return $aOut;
    }
}
