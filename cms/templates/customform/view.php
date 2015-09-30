<?php

/**
 * This is the "Full width" CMS template view
 *
 * @package     Nails
 * @subpackage  module-cms
 * @category    Template
 * @author      Nails Dev Team
 * @link
 */

echo $this->load->view('structure/header', getControllerData());
echo $mainbody;
echo $this->load->view('structure/footer', getControllerData());
