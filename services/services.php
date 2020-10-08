<?php

use Nails\CustomForms\Factory;
use Nails\CustomForms\Model;
use Nails\CustomForms\Resource;

return [
    'models'    => [
        'Form'             => function (): Model\Form {
            if (class_exists('\App\CustomForms\Model\Form')) {
                return new \App\CustomForms\Model\Form();
            } else {
                return new Model\Form();
            }
        },
        'FormNotification' => function (): Model\Form\Notification {
            if (class_exists('\App\CustomForms\Model\Form\Notification')) {
                return new \App\CustomForms\Model\Form\Notification();
            } else {
                return new Model\Form\Notification();
            }
        },
        'Response'         => function (): Model\Response {
            if (class_exists('\App\CustomForms\Model\Response')) {
                return new \App\CustomForms\Model\Response();
            } else {
                return new Model\Response();
            }
        },
    ],
    'factories' => [
        'EmailFormSubmitted'       => function (): Factory\Email\Form\Submitted {
            if (class_exists('\App\CustomForms\Factory\Email\Form\Submitted')) {
                return new \App\CustomForms\Factory\Email\Form\Submitted();
            } else {
                return new Factory\Email\Form\Submitted();
            }
        },
        'EmailFormSubmittedThanks' => function (): Factory\Email\Form\Submitted\Thanks {
            if (class_exists('\App\CustomForms\Factory\Email\Form\Submitted\Thanks')) {
                return new \App\CustomForms\Factory\Email\Form\Submitted\Thanks();
            } else {
                return new Factory\Email\Form\Submitted\Thanks();
            }
        },
    ],
    'resources' => [
        'Form'             => function ($mObj): Resource\Form {
            if (class_exists('\App\CustomForms\Resource\Form')) {
                return new \App\CustomForms\Resource\Form($mObj);
            } else {
                return new Resource\Form($mObj);
            }
        },
        'FormNotification' => function ($mObj): Resource\Form\Notification {
            if (class_exists('\App\CustomForms\Resource\Form\Notification')) {
                return new \App\CustomForms\Resource\Form\Notification($mObj);
            } else {
                return new Resource\Form\Notification($mObj);
            }
        },
        'Response'         => function ($mObj): Resource\Response {
            if (class_exists('\App\CustomForms\Resource\Response')) {
                return new \App\CustomForms\Resource\Response($mObj);
            } else {
                return new Resource\Response($mObj);
            }
        },
    ],
];
