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
        <li class="tab">
            <a href="#" data-tab="advanced">
                Advanced
            </a>
        </li>
    </ul>
    <section class="tabs">
        <div class="tab-page form active">
            <div class="fieldset">
            <?php

            $aField = array(
                'key'         => 'label',
                'label'       => 'Label',
                'placeholder' => 'Define the form\'s label',
                'required'    => true,
                'default'     => !empty($form->label) ? $form->label : ''
            );
            echo form_field($aField);

            // --------------------------------------------------------------------------

            $aField = array(
                'key'     => 'header',
                'label'   => 'Header',
                'default' => !empty($form->header) ? $form->header : ''
            );
            echo form_field_cms_widgets($aField);

            // --------------------------------------------------------------------------

            $aField = array(
                'key'     => 'footer',
                'label'   => 'Footer',
                'default' => !empty($form->footer) ? $form->footer : ''
            );
            echo form_field_cms_widgets($aField);

            // --------------------------------------------------------------------------

            $aField = array(
                'key'         => 'cta_label',
                'label'       => 'Button Label',
                'placeholder' => 'Define the text on the form\'s submit button, defaults to "Submit".',
                'default'     => !empty($form->cta->label) ? $form->cta->label : ''
            );
            echo form_field($aField);

            // --------------------------------------------------------------------------

            $aField = array(
                'key'        => 'has_captcha',
                'label'      => 'Captcha',
                'default'    => !empty($form->form->has_captcha),
                'info'       => $bIsCaptchaEnabled ? '' : 'Captcha Module has not been configured; this field will silently be ignored until Captcha is configured.',
                'info_class' => $bIsCaptchaEnabled ? '' : 'alert alert-warning'
            );
            echo form_field_boolean($aField);

            // --------------------------------------------------------------------------

            $aField = array(
                'key'     => 'is_minimal',
                'label'   => 'Minimal Layout',
                'default' => !empty($form->is_minimal),
                'info'    => 'When minimal, the form will not feature the site\'s header and footer.',
            );
            echo form_field_boolean($aField);

            ?>
            </div>
        </div>
        <div class="tab-page fields">
            <?php

            if (form_error('fields')) {

                ?>
                <div class="alert alert-danger">
                    <?=form_error('fields')?>
                </div>
                <?php
            }

            $aFields = !empty($form->form->fields->data) ? $form->form->fields->data : array();
            echo adminLoadFormBuilderView('custom-form-fields', 'fields', $aFields);

            ?>
        </div>
        <div class="tab-page submission">
            <div class="fieldset">
                <?php

                $aField = array(
                    'key'         => 'notification_email',
                    'label'       => 'Notify',
                    'placeholder' => 'A comma separated list of email addresses to notify when a form is submitted.',
                    'default'     => !empty($form->notification_email) ? implode(', ', $form->notification_email) : ''
                );

                echo form_field($aField);

                // --------------------------------------------------------------------------

                $aField = array(
                    'key'     => 'thankyou_email',
                    'label'   => 'Email',
                    'info'    => 'Send the user a thank you email',
                    'id'      => 'do-send-thankyou',
                    'default' => !empty($form->thankyou_email->send)
                );

                echo form_field_boolean($aField);

                // --------------------------------------------------------------------------

                echo '<div id="send-thankyou-options">';
                $aField = array(
                    'key'         => 'thankyou_email_subject',
                    'label'       => 'Subject',
                    'placeholder' => 'Define the subject of the thank you email',
                    'default'     => !empty($form->thankyou_email->subject) ? $form->thankyou_email->subject : ''
                );
                echo form_field($aField);

                // --------------------------------------------------------------------------

                $aField = array(
                    'key'         => 'thankyou_email_body',
                    'label'       => 'Body',
                    'placeholder' => 'Define the body of the thank you email',
                    'class'       => 'wysiwyg-basic',
                    'default'     => !empty($form->thankyou_email->body) ? $form->thankyou_email->body : ''
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
                    'key'         => 'thankyou_page_title',
                    'label'       => 'Title',
                    'placeholder' => 'Define the title of the thank you page',
                    'required'    => true,
                    'default'     => !empty($form->thankyou_page->title) ? $form->thankyou_page->title : ''
                );
                echo form_field($aField);

                // --------------------------------------------------------------------------

                $aField = array(
                    'key'         => 'thankyou_page_body',
                    'label'       => 'Body',
                    'placeholder' => 'Define the body of the thank you page',
                    'default'     => !empty($form->thankyou_page->body) ? $form->thankyou_page->body : ''
                );
                echo form_field_cms_widgets($aField);

                ?>
            </div>
        </div>
        <div class="tab-page advanced">
            <div class="fieldset">
                <?php

                $aField = array(
                    'key'         => 'cta_attributes',
                    'label'       => 'Button Attributes',
                    'placeholder' => 'Define any custom attributes which should be attached to the button.',
                    'default'     => !empty($form->cta->attributes) ? $form->cta->attributes : ''
                );
                echo form_field($aField);

                // --------------------------------------------------------------------------

                $aField = array(
                    'key'         => 'form_attributes',
                    'label'       => 'Form Attributes',
                    'placeholder' => 'Define any custom attributes which should be attached to the form.',
                    'default'     => !empty($form->form_attributes) ? $form->form_attributes : ''
                );
                echo form_field($aField);

                ?>
            </div>
        </div>
    </section>
    <div class="admin-floating-controls">
        <button type="submit" class="btn btn-primary">
            Save Changes
        </button>
    </div>
    <?=form_close()?>
</div>
