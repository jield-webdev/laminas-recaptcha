<?php

namespace CirclicalRecaptcha\Factory\Validator;

use CirclicalRecaptcha\Form\Validator\RecaptchaValidator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Override;
use Psr\Container\ContainerInterface;

class RecaptchaValidatorFactory implements FactoryInterface
{
    private const int DEFAULT_TIMEOUT = 900;

    #[Override]
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RecaptchaValidator
    {
        $config = $container->get('config');
        $config = $config['circlical']['recaptcha'];

        $validator = new RecaptchaValidator(
            secret: $config['server'] ?? 'not configured',
            responseTimeout: $config['default_timeout'] ?? self::DEFAULT_TIMEOUT
        );

        if (!empty($config['bypass'])) {
            $validator->setCaptchaBypassed(captchaBypassed: $config['bypass']);
        }

        return $validator;
    }
}