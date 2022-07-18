CirclicalRecaptcha
------------------

This repo is a fork of https://github.com/Saeven/zf2-circlical-recaptcha and upgraded to support Laminas
This library only supports Google ReCaptcha v3, for support of Google ReCaptcha v2 please use
the [Main repo](https://github.com/Saeven/zf2-circlical-recaptcha)

## Implementation

The system uses `form_elements` factories so the captcha form element cannot be directly called in the form constructor,
but have to be called via an `init()` call

Here is an example of a registration form using captcha form element

Install the module via composer:

```shell
composer require jield-webdev/laminas-recaptcha
```

And modify `circlical.recaptcha.local.php` and change the values with the values which can be found on
the [ReCaptcha dashboard](https://www.google.com/recaptcha/about/). Make sure you register a v3 application

```shell
<?php

declare(strict_types=1);

namespace Admin\Form\User;

use CirclicalRecaptcha\Form\Element\Recaptcha;
use Contact\Entity\OptIn;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

use function _;
use function sprintf;

final class Register extends Form
{
    public function __construct(private readonly EntityManager $entityManager)
    {
        parent::__construct();
    }

    public function init(): void
    {
        $this->setAttribute('action', '');

        $this->add(
            [
                'name' => 'firstName',
                'type' => Text::class,
                'options' => [
                    'label' => _('txt-first-name'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-give-your-first-name'),
                ],
            ]
        );
        $this->add(
            [
                'name' => 'lastName',
                'type' => Text::class,
                'options' => [
                    'label' => _('txt-last-name'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-give-your-last-name'),
                ],
            ]
        );
        $this->add(
            [
                'name' => 'email',
                'type' => Email::class,
                'options' => [
                    'label' => _('txt-company-email-address'),
                ],
                'attributes' => [
                    'placeholder' => _('txt-give-your-company-email-address'),
                ],
            ]
        );       
        $this->add(
            [
                'name' => 'g-recaptcha-response',
                'type' => Recaptcha::class,
            ]
        );
        $this->add(
            [
                'name' => 'csrf',
                'type' => Csrf::class,
            ]
        );
        $this->add(
            [
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-register'),
                ],
            ]
        );
    }
}
```

Register the form in the `module.config.php`, use a ConfigAbstractFactory pattern to register more services in the form
or use an invokable when no dependencies are needed

```php
'form_elements' => [
    'factories' => [
        Register::class => ConfigAbstractFactory::class,
    ],
],
```

The form has to be injected in the `Controller` via the `FormElementManager`, when using the `ConfigAbstractFactory`
system this can be done as follows:

```php
UserController::class => [
   'FormElementManager'
],
```

In the controller the form can be called in the following way

```php
//To properly load the captcha, we need to use the formElementManager to get the form
$form = $this->formElementManager->get(Register::class);
```