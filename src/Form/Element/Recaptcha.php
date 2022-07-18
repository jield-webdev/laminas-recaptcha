<?php

namespace CirclicalRecaptcha\Form\Element;

use CirclicalRecaptcha\Form\Validator\RecaptchaValidator;
use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;

class Recaptcha extends Element implements InputProviderInterface
{
    public const ELEMENT_TYPE = 'recaptcha';

    protected $attributes = [
        'type' => self::ELEMENT_TYPE,
    ];

    public function __construct(private string $secret)
    {
        parent::__construct();
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getInputSpecification(): array
    {
        return [
            'name'       => $this->getName(),
            'required'   => true,
            'validators' => [
                ['name' => RecaptchaValidator::class]
            ],
        ];
    }
}