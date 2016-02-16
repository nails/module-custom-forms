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
        },

        /**
         * Default Values
         */
        'DefaultValueCustom' => function () {
            if (class_exists('\App\CustomForms\DefaultValue\Custom')) {
                return new \App\CustomForms\DefaultValue\Custom();
            } else {
                return new \Nails\CustomForms\DefaultValue\Custom();
            }
        },
        'DefaultValueNone' => function () {
            if (class_exists('\App\CustomForms\DefaultValue\None')) {
                return new \App\CustomForms\DefaultValue\None();
            } else {
                return new \Nails\CustomForms\DefaultValue\None();
            }
        },
        'DefaultValueTimestamp' => function () {
            if (class_exists('\App\CustomForms\DefaultValue\Timestamp')) {
                return new \App\CustomForms\DefaultValue\Timestamp();
            } else {
                return new \Nails\CustomForms\DefaultValue\Timestamp();
            }
        },
        'DefaultValueUserEmail' => function () {
            if (class_exists('\App\CustomForms\DefaultValue\UserEmail')) {
                return new \App\CustomForms\DefaultValue\UserEmail();
            } else {
                return new \Nails\CustomForms\DefaultValue\UserEmail();
            }
        },
        'DefaultValueUserFirstName' => function () {
            if (class_exists('\App\CustomForms\DefaultValue\UserFirstName')) {
                return new \App\CustomForms\DefaultValue\UserFirstName();
            } else {
                return new \Nails\CustomForms\DefaultValue\UserFirstName();
            }
        },
        'DefaultValueUserId' => function () {
            if (class_exists('\App\CustomForms\DefaultValue\UserId')) {
                return new \App\CustomForms\DefaultValue\UserId();
            } else {
                return new \Nails\CustomForms\DefaultValue\UserId();
            }
        },
        'DefaultValueUserLastName' => function () {
            if (class_exists('\App\CustomForms\DefaultValue\UserLastName')) {
                return new \App\CustomForms\DefaultValue\UserLastName();
            } else {
                return new \Nails\CustomForms\DefaultValue\UserLastName();
            }
        },
        'DefaultValueUserName' => function () {
            if (class_exists('\App\CustomForms\DefaultValue\UserName')) {
                return new \App\CustomForms\DefaultValue\UserName();
            } else {
                return new \Nails\CustomForms\DefaultValue\UserName();
            }
        },

        /**
         * Field Types
         */
        'FieldTypeCheckbox' => function () {
            if (class_exists('\App\CustomForms\FieldType\Checkbox')) {
                return new \App\CustomForms\FieldType\Checkbox();
            } else {
                return new \Nails\CustomForms\FieldType\Checkbox();
            }
        },
        'FieldTypeDate' => function () {
            if (class_exists('\App\CustomForms\FieldType\Date')) {
                return new \App\CustomForms\FieldType\Date();
            } else {
                return new \Nails\CustomForms\FieldType\Date();
            }
        },
        'FieldTypeDateTime' => function () {
            if (class_exists('\App\CustomForms\FieldType\DateTime')) {
                return new \App\CustomForms\FieldType\DateTime();
            } else {
                return new \Nails\CustomForms\FieldType\DateTime();
            }
        },
        'FieldTypeEmail' => function () {
            if (class_exists('\App\CustomForms\FieldType\Email')) {
                return new \App\CustomForms\FieldType\Email();
            } else {
                return new \Nails\CustomForms\FieldType\Email();
            }
        },
        'FieldTypeFile' => function () {
            if (class_exists('\App\CustomForms\FieldType\File')) {
                return new \App\CustomForms\FieldType\File();
            } else {
                return new \Nails\CustomForms\FieldType\File();
            }
        },
        'FieldTypeHidden' => function () {
            if (class_exists('\App\CustomForms\FieldType\Hidden')) {
                return new \App\CustomForms\FieldType\Hidden();
            } else {
                return new \Nails\CustomForms\FieldType\Hidden();
            }
        },
        'FieldTypeNumber' => function () {
            if (class_exists('\App\CustomForms\FieldType\Number')) {
                return new \App\CustomForms\FieldType\Number();
            } else {
                return new \Nails\CustomForms\FieldType\Number();
            }
        },
        'FieldTypePassword' => function () {
            if (class_exists('\App\CustomForms\FieldType\Password')) {
                return new \App\CustomForms\FieldType\Password();
            } else {
                return new \Nails\CustomForms\FieldType\Password();
            }
        },
        'FieldTypeRadio' => function () {
            if (class_exists('\App\CustomForms\FieldType\Radio')) {
                return new \App\CustomForms\FieldType\Radio();
            } else {
                return new \Nails\CustomForms\FieldType\Radio();
            }
        },
        'FieldTypeSelect' => function () {
            if (class_exists('\App\CustomForms\FieldType\Select')) {
                return new \App\CustomForms\FieldType\Select();
            } else {
                return new \Nails\CustomForms\FieldType\Select();
            }
        },
        'FieldTypeTel' => function () {
            if (class_exists('\App\CustomForms\FieldType\Tel')) {
                return new \App\CustomForms\FieldType\Tel();
            } else {
                return new \Nails\CustomForms\FieldType\Tel();
            }
        },
        'FieldTypeText' => function () {
            if (class_exists('\App\CustomForms\FieldType\Text')) {
                return new \App\CustomForms\FieldType\Text();
            } else {
                return new \Nails\CustomForms\FieldType\Text();
            }
        },
        'FieldTypeTextarea' => function () {
            if (class_exists('\App\CustomForms\FieldType\Textarea')) {
                return new \App\CustomForms\FieldType\Textarea();
            } else {
                return new \Nails\CustomForms\FieldType\Textarea();
            }
        },
        'FieldTypeTime' => function () {
            if (class_exists('\App\CustomForms\FieldType\Time')) {
                return new \App\CustomForms\FieldType\Time();
            } else {
                return new \Nails\CustomForms\FieldType\Time();
            }
        },
        'FieldTypeUrl' => function () {
            if (class_exists('\App\CustomForms\FieldType\Url')) {
                return new \App\CustomForms\FieldType\Url();
            } else {
                return new \Nails\CustomForms\FieldType\Url();
            }
        }
    )
);
