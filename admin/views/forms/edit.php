<div class="group-custom-forms edit">
    <?=form_open(null, 'id="main-form"')?>
    <ul class="tabs">
        <li class="tab active">
            <a href="#" data-tab="form">
                Form Page
            </a>
        </li>
        <li class="tab">
            <a href="#" data-tab="fields">
                Form Fields
            </a>
        </li>
        <li class="tab">
            <a href="#" data-tab="submission">
                Submission Behaviour
            </a>
        </li>
        <li class="tab">
            <a href="#" data-tab="thankyou">
                Thank You Page
            </a>
        </li>
    </ul>
    <section class="tabs">
        <div class="tab-page form active">
            <div class="fieldset">
            <?php

            $aField = array(
                'key' => 'label',
                'label' => 'Label',
                'placeholder' => 'Define the form\'s label',
                'required' => true,
                'default' => !empty($form->label) ? $form->label : ''
            );
            echo form_field($aField);

            // --------------------------------------------------------------------------

            $aField = array(
                'key' => 'header',
                'label' => 'Header',
                'placeholder' => 'Define the form\'s header text',
                'default' => !empty($form->header) ? $form->header : ''
            );
            echo form_field_wysiwyg($aField);

            // --------------------------------------------------------------------------

            $aField = array(
                'key' => 'footer',
                'label' => 'Footer',
                'placeholder' => 'Define the form\'s footer text',
                'default' => !empty($form->footer) ? $form->footer : ''
            );
            echo form_field_wysiwyg($aField);

            // --------------------------------------------------------------------------

            $aField = array(
                'key' => 'cta_label',
                'label' => 'Call To Action',
                'placeholder' => 'Define the text on the form\'s submit button, defaults to "Submit".',
                'default' => !empty($form->cta_label) ? $form->cta_label : ''
            );
            echo form_field($aField);

            // --------------------------------------------------------------------------

            $aField = array(
                'key' => 'form_attributes',
                'label' => 'Form Attributes',
                'placeholder' => 'Define any custom attribtues which should be attached to the form.',
                'default' => !empty($form->form_attributes) ? $form->form_attributes : ''
            );
            echo form_field($aField);

            ?>
            </div>
        </div>
        <div class="tab-page fields">
            <div class="fieldset">
                <div class="table-responsive">
                    <table id="form-fields">
                        <thead>
                            <tr>
                                <th class="order">
                                    Order
                                </th>
                                <th class="type">
                                    Type
                                </th>
                                <th class="field-label">
                                    Label
                                </th>
                                <th class="field-sub-label">
                                    Sub Label
                                </th>
                                <th class="placeholder">
                                    Placeholder
                                </th>
                                <th class="required">
                                    Required
                                </th>
                                <th class="default">
                                    Default Value
                                </th>
                                <th class="attributes">
                                    Custom Field Attributes
                                </th>
                                <th class="remove">
                                    &nbsp;
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php

                        if (!empty($_POST['fields'])) {

                            $aFields = $_POST['fields'];

                            //  Cast as objects to match database output
                            foreach ($aFields as &$aField) {

                                if (empty($aField['options'])) {
                                    $aField['options'] = array();
                                }

                                foreach ($aField['options'] as &$aOption) {
                                    $aOption = (object) $aOption;
                                }


                                $aField = (object) $aField;
                            }


                        } elseif (!empty($form->fields)) {

                            $aFields = $form->fields;

                        } else {

                            $aFields = array();
                        }

                        $i = 0;
                        $aTypes = array(
                            'TEXT' => 'Text',
                            'NUMBER' => 'Number',
                            'EMAIL' => 'Email address',
                            'TEL' => 'Telephone',
                            'TEXTAREA' => 'Textarea',
                            'SELECT' => 'Dropdown',
                            'CHECKBOX' => 'Checkbox',
                            'RADIO' => 'Radio',
                            'DATE' => 'Date',
                            'TIME' => 'Time',
                            'DATETIME' => 'Datetime',
                            'HIDDEN' => 'Hidden'
                        );

                        $aDefaultValueTypes = array(
                            'None' => 'No default value',
                            'USER_ID' => 'User ID',
                            'BUSINESS_ID' => 'Business ID',
                            'BUSINESS_NAME' => 'Business Name',
                            'USER_NAME' => 'User Name',
                            'USER_EMAIL' => 'User Email',
                            'USER_TELEPHONE' => 'User Telephone',
                            'CURRENT_TIMESTAMP' => 'Current Timestamp',
                            'CUSTOM' => 'Custom'
                        );

                        foreach ($aFields as $oField) {

                            ?>
                            <tr>
                                <td class="order">
                                    <b class="fa fa-bars handle"></b>
                                    <?php

                                    echo form_hidden(
                                        'fields[' . $i . '][id]',
                                        !empty($oField->id) ? $oField->id : ''
                                    );

                                    ?>
                                </td>
                                <td class="type">
                                    <?php

                                    echo form_dropdown(
                                        'fields[' . $i . '][type]',
                                        $aTypes,
                                        set_value('fields[' . $i . '][type]', $oField->type),
                                        'class="select2 field-type"'
                                    );

                                    ?>
                                    <a href="#form-field-options-<?=$i?>" class="fancybox awesome small orange">
                                        Manage Options
                                    </a>
                                </td>
                                <td class="field-label">
                                    <?php

                                    echo form_input(
                                        'fields[' . $i . '][label]',
                                        set_value('fields[' . $i . '][label]', $oField->label)
                                    );

                                    ?>
                                </td>
                                <td class="field-sub-label">
                                    <?php

                                    echo form_input(
                                        'fields[' . $i . '][sub_label]',
                                        set_value('fields[' . $i . '][sub_label]', $oField->sub_label)
                                    );

                                    ?>
                                </td>
                                <td class="placeholder">
                                    <?php

                                    echo form_input(
                                        'fields[' . $i . '][placeholder]',
                                        set_value('fields[' . $i . '][placeholder]', $oField->placeholder)
                                    );

                                    ?>
                                </td>
                                <td class="required">
                                    <?php

                                    echo form_checkbox(
                                        'fields[' . $i . '][is_required]',
                                        true,
                                        !empty($oField->is_required)
                                    );

                                    ?>
                                </td>
                                <td class="default">
                                    <?php

                                    echo form_dropdown(
                                        'fields[' . $i . '][default_value]',
                                        $aDefaultValueTypes,
                                        set_value('fields[' . $i . '][default_value]', $oField->default_value),
                                        'class="select2 field-default"'
                                    );

                                    ?>
                                    <?php

                                    echo form_input(
                                        'fields[' . $i . '][default_value_custom]',
                                        set_value('fields[' . $i . '][default_value_custom]', $oField->default_value_custom)
                                    );

                                    ?>
                                </td>
                                <td class="attributes">
                                    <?php

                                    echo form_input(
                                        'fields[' . $i . '][custom_attributes]',
                                        set_value('fields[' . $i . '][custom_attributes]', $oField->custom_attributes)
                                    );

                                    ?>
                                </td>
                                <td class="remove">
                                    <a href="#" class="remove-field" data-field-number="<?=$i?>">
                                        <b class="fa fa-times-circle fa-lg"></b>
                                    </a>
                                </td>
                            </tr>
                            <?php

                            $i++;
                        }

                        ?>
                        </tbody>
                    </table>
                </div>
                <p>
                    <a href="#" id="add-field" class="awesome green small">
                        Add Field
                    </a>
                </p>
            </div>
        </div>
        <div class="tab-page submission">
            <div class="fieldset">
                <?php

                $aField = array(
                    'key' => 'notification_email',
                    'label' => 'Notify',
                    'placeholder' => 'A comma separated list of email addresses to notify when a form is submitted.',
                    'default' => !empty($form->notification_email) ? implode(',', $form->notification_email) : ''
                );

                echo form_field($aField);

                // --------------------------------------------------------------------------

                $aField = array(
                    'key' => 'thankyou_email',
                    'label' => 'Email',
                    'info' => 'Send the user a thank you email',
                    'id' => 'do-send-thankyou',
                    'default' => !empty($form->thankyou_email) ? $form->thankyou_email : false
                );

                echo form_field_boolean($aField);

                // --------------------------------------------------------------------------

                echo '<div id="send-thankyou-options">';
                $aField = array(
                    'key' => 'thankyou_email_subject',
                    'label' => 'Subject',
                    'placeholder' => 'Define the subject of the thank you email',
                    'default' => !empty($form->thankyou_email_subject) ? $form->thankyou_email_subject : ''
                );
                echo form_field($aField);

                // --------------------------------------------------------------------------

                $aField = array(
                    'key' => 'thankyou_email_body',
                    'label' => 'Body',
                    'placeholder' => 'Define the body of the thank you email',
                    'default' => !empty($form->thankyou_email_body) ? $form->thankyou_email_body : ''
                );
                echo form_field_wysiwyg($aField);
                echo '</div>';

                ?>
            </div>
        </div>
        <div class="tab-page thankyou">
            <div class="fieldset">
                <?php

                $aField = array(
                    'key' => 'thankyou_page_title',
                    'label' => 'Subject',
                    'placeholder' => 'Define the title of the thank you page',
                    'default' => !empty($form->thankyou_page_title) ? $form->thankyou_page_title : ''
                );
                echo form_field($aField);

                // --------------------------------------------------------------------------

                $aField = array(
                    'key' => 'thankyou_page_body',
                    'label' => 'Body',
                    'placeholder' => 'Define the body of the thank you page',
                    'default' => !empty($form->thankyou_page_body) ? $form->thankyou_page_body : ''
                );
                echo form_field_wysiwyg($aField);

                ?>
            </div>
        </div>
    </section>
    <div id="field-options">
    <?php

    $i = 0;
    foreach ($aFields as $oField) {

        ?>
        <div id="form-field-options-<?=$i?>" class="form-field-options">
            <table data-option-count="<?=!empty($oField->options) ? count($oField->options) : 0?>">
                <thead>
                    <tr>
                        <th class="option-label">
                            Label
                        </th>
                        <th class="option-selected">
                            Selected
                        </th>
                        <th class="option-disabled">
                            Disabled
                        </th>
                        <th class="option-remove">
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    if (!empty($oField->options)) {

                        $x = 0;

                        foreach ($oField->options as $oOption) {

                            ?>
                            <tr>
                                <td class="option-label">
                                    <?php

                                    echo form_input(
                                        'fields[' . $i . '][options][' . $x . '][label]',
                                        $oOption->label
                                    );

                                    echo form_hidden(
                                        'fields[' . $i . '][options][' . $x . '][id]',
                                        !empty($oOption->id) ? $oOption->id : ''
                                    );

                                    ?>
                                </td>
                                <td class="option-selected">
                                    <?php

                                    echo form_checkbox(
                                        'fields[' . $i . '][options][' . $x . '][is_selected]',
                                        true,
                                        !empty($oOption->is_selected)
                                    );

                                    ?>
                                </td>
                                <td class="option-disabled">
                                    <?php

                                    echo form_checkbox(
                                        'fields[' . $i . '][options][' . $x . '][is_disabled]',
                                        true,
                                        !empty($oOption->is_disabled)
                                    );

                                    ?>
                                </td>
                                <td class="option-remove">
                                    <a href="#" class="remove-option">
                                        <b class="fa fa-times-circle fa-lg"></b>
                                    </a>
                                </td>
                            </tr>
                            <?php

                            $x++;
                        }
                    }

                    ?>
                </tbody>
            </table>
            <p>
                <button type="button" class="awesome small green add-option" data-field-number="<?=$i?>">
                    Add Option
                </button>
            </p>
        </div>
        <?php

        $i++;
    }

    ?>
    </div>
    <p>
        <button type="submit" class="awesome">
            Save Changes
        </button>
    </p>
    <?=form_close()?>
</div>
<script type="template/mustache" id="template-field">
<tr>
    <td class="order">
        <b class="fa fa-bars handle"></b>
    </td>
    <td class="type">
        <?=form_dropdown('fields[{{fieldNumber}}][type]', $aTypes, null, 'class="select2 field-type"')?>
        <a href="#form-field-options-{{fieldNumber}}" class="fancybox awesome small orange">
            Manage Options
        </a>
    </td>
    <td class="field-label">
        <?=form_input('fields[{{fieldNumber}}][label]')?>
    </td>
    <td class="field-sub-label">
        <?=form_input('fields[{{fieldNumber}}][sub_label]')?>
    </td>
    <td class="placeholder">
        <?=form_input('fields[{{fieldNumber}}][placeholder]')?>
    </td>
    <td class="required">
        <?=form_checkbox('fields[{{fieldNumber}}][is_required]', true)?>
    </td>
    <td class="default">
        <?=form_dropdown('fields[{{fieldNumber}}][default_value]', $aDefaultValueTypes, null, 'class="select2 field-default"')?>
        <?=form_input('fields[{{fieldNumber}}][default_value_custom]')?>
    </td>
    <td class="attributes">
        <?=form_input('fields[{{fieldNumber}}][custom_attributes]')?>
    </td>
    <td class="remove">
        <a href="#" class="remove-field" data-field-number="{{fieldNumber}}">
            <b class="fa fa-times-circle fa-lg"></b>
        </a>
    </td>
</tr>
</script>
<script type="template/mustache" id="template-field-option-container">
<div id="form-field-options-{{fieldNumber}}" class="form-field-options">
    <table data-option-count="0">
        <thead>
            <tr>
                <th class="option-label">
                    Label
                </th>
                <th class="option-selected">
                    Selected
                </th>
                <th class="option-disabled">
                    Disabled
                </th>
                <th class="option-remove">
                    &nbsp;
                </th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <p>
        <button type="button" class="awesome small green add-option" data-field-number="{{fieldNumber}}">
            Add Option
        </button>
    </p>
</div>
</script>
<script type="template/mustache" id="template-field-option">
<tr>
    <td class="option-label">
        <?=form_input('fields[{{fieldNumber}}][options][{{optionNumber}}][label]')?>
    </td>
    <td class="option-selected">
        <?=form_checkbox('fields[{{fieldNumber}}][options][{{optionNumber}}][is_selected]', true)?>
    </td>
    <td class="option-disabled">
        <?=form_checkbox('fields[{{fieldNumber}}][options][{{optionNumber}}][is_disabled]', true)?>
    </td>
    <td class="option-remove">
        <a href="#" class="remove-option">
            <b class="fa fa-times-circle fa-lg"></b>
        </a>
    </td>
</tr>
</script>
