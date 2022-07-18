<?php

namespace CirclicalRecaptcha\Factory\Form\Element;

use CirclicalRecaptcha\Form\Element\Recaptcha;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class RecaptchaFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Recaptcha
    {
        $config = $container->get('config');

        return new Recaptcha($config['circlical']['recaptcha']['client'] ?? 'configure_me');
    }
}

