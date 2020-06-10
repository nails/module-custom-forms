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

                echo form_field([
                    'key'         => 'label',
                    'label'       => 'Label',
                    'placeholder' => 'Define the form\'s label',
                    'required'    => true,
                    'default'     => !empty($form->label) ? $form->label : '',
                ]);

                echo form_field_cms_widgets([
                    'key'     => 'header',
                    'label'   => 'Header',
                    'default' => !empty($form->header) ? $form->header : '',
                ]);

                echo form_field_cms_widgets([
                    'key'     => 'footer',
                    'label'   => 'Footer',
                    'default' => !empty($form->footer) ? $form->footer : '',
                ]);

                echo form_field([
                    'key'         => 'cta_label',
                    'label'       => 'Button Label',
                    'placeholder' => 'Define the text on the form\'s submit button, defaults to "Submit".',
                    'default'     => !empty($form->cta->label) ? $form->cta->label : '',
                ]);

                echo form_field_boolean([
                    'key'        => 'has_captcha',
                    'label'      => 'Captcha',
                    'default'    => !empty($form->form->has_captcha),
                    'info'       => $bIsCaptchaEnabled ? '' : 'Captcha Module has not been configured',
                    'info_class' => $bIsCaptchaEnabled ? '' : 'alert alert-warning',
                ]);

                echo form_field_boolean([
                    'key'     => 'is_minimal',
                    'label'   => 'Minimal Layout',
                    'default' => !empty($form->is_minimal),
                    'info'    => 'When minimal, the form will not feature the site\'s header and footer.',
                ]);

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

            $aFields = !empty($form->form->fields->data) ? $form->form->fields->data : [];
            echo adminLoadFormBuilderView('custom-form-fields', 'fields', $aFields);

            ?>
        </div>
        <div class="tab-page submission">
            <div class="fieldset">
                <?php

                echo form_field_dynamic_table([
                    'key'     => 'notifications',
                    'label'   => 'Notify',
                    'id'      => 'custom-form-notifications',
                    'default' => $aNotifications,
                    'columns' => [
                        'Email'     => form_email(
                            'notifications[{{index}}][email]',
                            '{{email}}',
                            'placeholder="someone@example.com" class="js-notification-email"'
                        ),
                        'Condition' =>
                            '<div class="row">' .
                            '<div class="col-md-2">' .
                            form_dropdown(
                                'notifications[{{index}}][condition_enabled]',
                                [
                                    'Always',
                                    'When',
                                ],
                                null,
                                'class="js-notification-condition-enabled" data-dynamic-table-value="{{condition_enabled}}"'
                            ) .
                            '</div>' .
                            '<div class="col-md-4">' .
                            form_dropdown(
                                'notifications[{{index}}][condition_field_id]',
                                [],
                                null,
                                'class="js-notification-condition-field-id" data-dynamic-table-value="{{condition_field_id}}"'
                            ) .
                            '</div>' .
                            '<div class="col-md-2">' .
                            form_dropdown(
                                'notifications[{{index}}][condition_operator]',
                                $aNotificationOperators,
                                null,
                                'class="js-notification-condition-operator" data-dynamic-table-value="{{condition_operator}}"'
                            ) .
                            '</div>' .
                            '<div class="col-md-4">' .
                            form_input(
                                'notifications[{{index}}][condition_value]',
                                '{{condition_value}}',
                                'class="js-notification-condition-value" placeholder="value"'
                            ) .
                            '</div>' .
                            '</div>',
                    ],
                ]);

                echo form_field_boolean([
                    'key'     => 'thankyou_email',
                    'label'   => 'Email',
                    'info'    => 'Send the user a thank you email (will only be sent if logged in, and will be sent to the logged in user)',
                    'default' => !empty($form->thankyou_email->send),
                    'data'    => [
                        'revealer' => 'thankyou-email',
                    ],
                ]);

                echo form_field([
                    'key'         => 'thankyou_email_subject',
                    'label'       => 'Subject',
                    'placeholder' => 'Define the subject of the thank you email',
                    'default'     => !empty($form->thankyou_email->subject) ? $form->thankyou_email->subject : '',
                    'data'        => [
                        'revealer'  => 'thankyou-email',
                        'reveal-on' => true,
                    ],
                ]);

                echo form_field_wysiwyg([
                    'key'         => 'thankyou_email_body',
                    'label'       => 'Body',
                    'placeholder' => 'Define the body of the thank you email',
                    'class'       => 'wysiwyg-basic',
                    'default'     => !empty($form->thankyou_email->body) ? $form->thankyou_email->body : '',
                    'data'        => [
                        'revealer'  => 'thankyou-email',
                        'reveal-on' => true,
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="tab-page thankyou">
            <div class="fieldset">
                <?php

                echo form_field([
                    'key'         => 'thankyou_page_title',
                    'label'       => 'Title',
                    'placeholder' => 'Define the title of the thank you page',
                    'required'    => true,
                    'default'     => !empty($form->thankyou_page->title) ? $form->thankyou_page->title : '',
                ]);

                echo form_field_cms_widgets([
                    'key'         => 'thankyou_page_body',
                    'label'       => 'Body',
                    'placeholder' => 'Define the body of the thank you page',
                    'default'     => !empty($form->thankyou_page->body) ? $form->thankyou_page->body : '',
                ]);

                ?>
            </div>
        </div>
        <div class="tab-page advanced">
            <div class="fieldset">
                <?php

                echo form_field([
                    'key'         => 'cta_attributes',
                    'label'       => 'Button Attributes',
                    'placeholder' => 'Define any custom attributes which should be attached to the button.',
                    'default'     => !empty($form->cta->attributes) ? $form->cta->attributes : '',
                ]);

                echo form_field([
                    'key'         => 'form_attributes',
                    'label'       => 'Form Attributes',
                    'placeholder' => 'Define any custom attributes which should be attached to the form.',
                    'default'     => !empty($form->form_attributes) ? $form->form_attributes : '',
                ]);

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
