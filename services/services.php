<?php

return array(
    'models' => array(
        'Form' => function () {
            if (class_exists('\App\CustomForms\Model\Form')) {
                return new \App\CustomForms\Model\Form();
            } else {
                return new \Nails\CustomForms\Model\Form();
            }
        },
        'FormField' => function () {
            if (class_exists('\App\CustomForms\Model\FormField')) {
                return new \App\CustomForms\Model\FormField();
            } else {
                return new \Nails\CustomForms\Model\FormField();
            }
        },
        'FormFieldOption' => function () {
            if (class_exists('\App\CustomForms\Model\FormFieldOption')) {
                return new \App\CustomForms\Model\FormFieldOption();
            } else {
                return new \Nails\CustomForms\Model\FormFieldOption();
            }
        },
        'Response' => function () {
            if (class_exists('\App\CustomForms\Model\Response')) {
                return new \App\CustomForms\Model\Response();
            } else {
                return new \Nails\CustomForms\Model\Response();
            }
        }
    )
);
