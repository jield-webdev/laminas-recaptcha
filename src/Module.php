<?php

namespace CirclicalRecaptcha;

use CirclicalRecaptcha\Form\Element\Recaptcha;
use Laminas\ModuleManager\Feature;

final class Module implements Feature\ConfigProviderInterface, Feature\BootstrapListenerInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap($event): void
    {
        $application = $event->getApplication();
        $services    = $application->getServiceManager();
        $services->get('ViewHelperManager')->get('FormElement')->addType(Recaptcha::ELEMENT_TYPE, Recaptcha::ELEMENT_TYPE);
    }
}