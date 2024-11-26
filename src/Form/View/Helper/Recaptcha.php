<?php

namespace CirclicalRecaptcha\Form\View\Helper;

use Laminas\Form\View\Helper\FormElement;
use Override;

class Recaptcha extends FormElement
{
    #[Override]
    public function render($element): string
    {
        assert($element instanceof \CirclicalRecaptcha\Form\Element\Recaptcha);

        $scriptTag = sprintf(
            '<script src="//www.google.com/recaptcha/api.js?render=%s"></script>',
            $element->getSecret()
        );

        $callBackScript = <<<SCRIPT
            <input type="hidden" name="%s" value="" id="recaptchaResponse">
            <script>grecaptcha.ready(function() {
                    grecaptcha.execute('%s', { action: 'submit' }).then(function (token) {
                        const recaptchaResponse = document.getElementById('recaptchaResponse');
                        recaptchaResponse.value = token;
                    })
                 });
                </script>
            SCRIPT;
        $callBack       = sprintf($callBackScript, $element->getName(), $element->getSecret());

        return sprintf('%s%s', $scriptTag, $callBack);
    }
}