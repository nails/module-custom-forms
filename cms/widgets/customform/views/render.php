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

$sUuid       = md5(microtime(true));
$iFormId     = !empty($formId) ? (int) $formId: null;
$bShowLabel  = !empty($showLabel);
$bShowHeader = !empty($showHeader);
$bShowFooter = !empty($showFooter);

if (!empty($iFormId)) {

    $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');
    $oForm      = $oFormModel->getById($iFormId);

    ?>
    <div class="cms-widget cms-widget-custom-forms">
        <?php

        if ($bShowHeader) {
            echo cmsAreaWithData($oForm->header);
        }

        echo form_open('forms/' . $oForm->id, $oForm->form_attributes);

        $iCounter = 0;

        foreach ($oForm->fields as $oField) {

            $sId          = 'custom-form-' . $sUuid . '-' . $iCounter;
            $sFieldName   = 'field[' . $oField->id . ']';
            $sType        = $oField->type;
            $sLabel       = $oField->label;
            $sPlaceholder = $oField->placeholder;
            $sDefault     = $oField->default_value;
            $bRequired    = $oField->is_required;

            //  Prepare default value
            //  @todo

            ?>
            <div class="form-group">
                <label for="<?=$sId?>"><?=$sLabel?></label>
                <?php

                $aAttr = array(
                    'name=""',
                    'class="form-control"',
                    'id="' . $sId . '"',
                    'placeholder="' . $sPlaceholder . '"',
                    $bRequired ? 'required="required"' : ''

                );

                $aAttr = array_filter($aAttr);
                $aAttr = array_unique($aAttr);
                $sAttr = implode(' ', $aAttr);

                switch ($sType) {
                    case 'TEXTAREA':
                        echo form_textarea(
                            $sFieldName,
                            set_value($sFieldName, $sDefault),
                            $sAttr
                        );
                        break;

                    case 'EMAIL':
                        echo form_email(
                            $sFieldName,
                            set_value($sFieldName, $sDefault),
                            $sAttr
                        );
                        break;

                    case 'NUMBER':
                        echo form_number(
                            $sFieldName,
                            set_value($sFieldName, $sDefault),
                            $sAttr
                        );
                        break;

                    case 'TEL':
                        echo form_tel(
                            $sFieldName,
                            set_value($sFieldName, $sDefault),
                            $sAttr
                        );
                        break;

                    case 'URL':
                        echo form_url(
                            $sFieldName,
                            set_value($sFieldName, $sDefault),
                            $sAttr
                        );
                        break;

                    case 'SELECT':
                        echo form_dropdown(
                            $sFieldName,
                            array('@todo', '@todo'),
                            set_value($sFieldName, $sDefault),
                            $sAttr
                        );
                        break;

                    case 'CHECKBOX':
                    case 'RADIO':
                        dump('@todo - checkbox and radio fields');
                        break;

                    case 'DATE':
                        dump('@todo - date and time fields');
                        break;

                    case 'TIME':
                        dump('@todo - date and time fields');
                        break;

                    case 'DATETIME':
                        dump('@todo - date and time fields');
                        break;

                    case 'HIDDEN':
                        echo form_hidden(
                            $sFieldName,
                            set_value($sFieldName, $sDefault)
                        );
                        break;

                    case 'TEXT':
                    default:
                        echo form_input(
                            $sFieldName,
                            set_value($sFieldName, $sDefault),
                            $sAttr
                        );
                        break;
                }


                ?>
            </div>
            <?php

            $iCounter++;
        }

        ?>
        <p>
            <button type="submit" class="btn btn-primary">
                <?=$oForm->cta_label ?: 'Submit'?>
            </button>
        </p>
        <?php

        echo form_close();

        if ($bShowFooter) {
            echo cmsAreaWithData($oForm->footer);
        }

        ?>
    </div>
    <?php

}
