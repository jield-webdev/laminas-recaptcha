<?php

namespace CirclicalRecaptcha\Factory\Form\Element;

use CirclicalRecaptcha\Form\Element\Recaptcha;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Override;
use Psr\Container\ContainerInterface;

class RecaptchaFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Recaptcha
    {
        $config = $container->get('config');

        return new Recaptcha(secret: $config['circlical']['recaptcha']['client'] ?? 'configure_me');
    }
}

