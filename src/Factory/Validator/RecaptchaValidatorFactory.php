<?php

namespace CirclicalRecaptcha\Factory\Validator;

use CirclicalRecaptcha\Form\Validator\RecaptchaValidator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class RecaptchaValidatorFactory implements FactoryInterface
{
    private const DEFAULT_TIMEOUT = 900;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RecaptchaValidator
    {
        $config = $container->get('config');
        $config = $config['circlical']['recaptcha'];

        $validator = new RecaptchaValidator(
            $config['server'] ?? 'not configured',
            $config['default_timeout'] ?? self::DEFAULT_TIMEOUT
        );

        if (!empty($config['bypass'])) {
            $validator->setCaptchaBypassed($config['bypass']);
        }

        return $validator;
    }
}