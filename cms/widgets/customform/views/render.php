<?php

/**
 * This is the "Custom Forms" CMS widget view
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Widget
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

$iFormId = !empty($formId) ? (int) $formId: null;

if (!empty($iFormId)) {

    $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');
    $oForm      = $oFormModel->getById($iFormId);

    ?>
    <div class="cms-widget cms-widget-custom-forms">
        <?php dump($oForm) ?>
    </div>
    <?php

}
