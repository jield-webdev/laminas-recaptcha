<?php

namespace CirclicalRecaptcha\Form\View\Helper;

use Laminas\Form\Element\Captcha;
use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormElement;

class Recaptcha extends FormElement
{
    public function render(ElementInterface|Captcha $element): string
    {
        $scriptTag = sprintf(
            '<script src="//www.google.com/recaptcha/api.js?render=%s"></script>',
            $element->getSecret()
        );

        $callBackScript = <<<SCRIPT
            <input type="hidden" name="%s" value="" id="recaptchaResponse">
            <script>grecaptcha.ready(function() {
                    grecaptcha.execute('%s', { action: 'submit' }).then(function (token) {
                        var recaptchaResponse = document.getElementById('recaptchaResponse');
                        recaptchaResponse.value = token;
                    })
                 });
                </script>
            SCRIPT;
        $callBack = sprintf($callBackScript, $element->getName(), $element->getSecret());

        return sprintf('%s%s', $scriptTag, $callBack);
    }
}