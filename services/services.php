<?php

return [
    'models'    => [
        'Form'             => function (): \Nails\CustomForms\Model\Form {
            if (class_exists('\App\CustomForms\Model\Form')) {
                return new \App\CustomForms\Model\Form();
            } else {
                return new \Nails\CustomForms\Model\Form();
            }
        },
        'Response'         => function (): \Nails\CustomForms\Model\Response {
            if (class_exists('\App\CustomForms\Model\Response')) {
                return new \App\CustomForms\Model\Response();
            } else {
                return new \Nails\CustomForms\Model\Response();
            }
        },
    ],
    'resources' => [
        'Form'             => function ($mObj): \Nails\CustomForms\Resource\Form {
            if (class_exists('\App\CustomForms\Resource\Form')) {
                return new \App\CustomForms\Resource\Form($mObj);
            } else {
                return new \Nails\CustomForms\Resource\Form($mObj);
            }
        },
        'Response'         => function ($mObj): \Nails\CustomForms\Resource\Response {
            if (class_exists('\App\CustomForms\Resource\Response')) {
                return new \App\CustomForms\Resource\Response($mObj);
            } else {
                return new \Nails\CustomForms\Resource\Response($mObj);
            }
        },
    ],
];
