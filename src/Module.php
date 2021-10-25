<?php

namespace CirclicalRecaptcha;

use CirclicalRecaptcha\Form\Element\Recaptcha;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature;

final class Module implements Feature\ConfigProviderInterface, Feature\BootstrapListenerInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e): void
    {
        $application = $e->getApplication();
        $services    = $application->getServiceManager();
        $services->get('ViewHelperManager')->get('FormElement')->addType(Recaptcha::ELEMENT_TYPE, Recaptcha::ELEMENT_TYPE);
    }
}