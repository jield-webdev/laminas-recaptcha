<?php

namespace CirclicalRecaptcha;

use CirclicalRecaptcha\Factory\Form\Element\RecaptchaFactory;
use CirclicalRecaptcha\Factory\Validator\RecaptchaValidatorFactory;
use CirclicalRecaptcha\Form\Element\Recaptcha;
use CirclicalRecaptcha\Form\Validator\RecaptchaValidator;
use CirclicalRecaptcha\Form\View\Helper\Recaptcha as RecaptchaHelper;

return [
    'form_elements' => [
        'factories' => [
            Recaptcha::class => RecaptchaFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'recaptcha' => RecaptchaHelper::class,
        ],
    ],
    'validators' => [
        'factories' => [
            RecaptchaValidator::class => RecaptchaValidatorFactory::class,
        ],
    ],
];