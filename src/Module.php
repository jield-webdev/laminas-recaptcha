<?php

namespace CirclicalRecaptcha;

use CirclicalRecaptcha\Form\Element\Recaptcha;
use Laminas\EventManager\EventInterface;
use Laminas\Form\View\Helper\FormElement;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Override;

final class Module implements ConfigProviderInterface
{
    #[Override]
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}