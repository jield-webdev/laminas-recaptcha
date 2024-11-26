<?php

namespace CirclicalRecaptcha\Form\Validator;

use ErrorException;
use Laminas\Validator\AbstractValidator;
use Override;

class RecaptchaValidator extends AbstractValidator
{
    public const string NOT_ANSWERED = 'not_answered';

    public const string EXPIRED = 'expired';

    public const string ERROR_MISSING_SECRET = 'missing-input-secret';

    public const string ERROR_INVALID_SECRET = 'invalid-input-secret';

    public const string ERROR_MISSING_INPUT_RESPONSE = 'missing-input-response';

    public const string ERROR_INVALID_INPUT_RESPONSE = 'invalid-input-response';

    public const string ERROR_CONNECTION_FAILED = 'connection-failed';

    private const string GOOGLE_REQUEST_URL = 'https://www.google.com/recaptcha/api/siteverify';

    protected array $messageTemplates
        = [
            self::ERROR_MISSING_SECRET         => 'The secret parameter is missing.',
            self::ERROR_INVALID_SECRET         => 'The secret parameter is invalid or malformed.',
            self::ERROR_MISSING_INPUT_RESPONSE => 'The response parameter is missing.',
            self::ERROR_INVALID_INPUT_RESPONSE => 'The response parameter is invalid or malformed.',
            self::NOT_ANSWERED                 => 'You must complete the challenge.',
            self::EXPIRED                      => 'Your form timed out, please try again.',
            self::ERROR_CONNECTION_FAILED      => 'The captcha could not be verified, please try again.',
        ];

    private array $errorCodes = [];

    private bool $captchaBypassed = false;

    public function __construct(private readonly string $secret, private readonly int $responseTimeout, ?array $options = null)
    {
        parent::__construct(options: $options);
    }

    public function isCaptchaBypassed(): bool
    {
        return $this->captchaBypassed;
    }

    public function setCaptchaBypassed(bool $captchaBypassed): void
    {
        $this->captchaBypassed = $captchaBypassed;
    }

    public function getErrorCodes(): array
    {
        return $this->errorCodes;
    }

    #[Override]
    public function isValid($value): bool
    {
        if ($this->captchaBypassed) {
            return true;
        }

        if (trim(string: (string)$value) === '' || trim(string: (string)$value) === '0') {
            $this->errorCodes[] = 'no-value-set';
            $this->error(messageKey: self::NOT_ANSWERED);

            return false;
        }

        // https://www.google.com/recaptcha/api/siteverify
        $ipAddress  = self::getIP();
        $requestUrl = self::GOOGLE_REQUEST_URL . '?' . http_build_query(data: [
                'secret'   => $this->secret,
                'response' => $value,
                'remoteip' => $ipAddress,
            ]);

        try {
            $googleResponse = @file_get_contents(filename: $requestUrl);
            if ($googleResponse === false) {
                throw new ErrorException(message: 'Site could not be reached');
            }

            $json = json_decode(json: $googleResponse, associative: true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->errorCodes[] = self::ERROR_INVALID_INPUT_RESPONSE;
                $this->error(messageKey: self::ERROR_INVALID_INPUT_RESPONSE);
            }
        } catch (ErrorException) {
            $this->errorCodes[] = self::ERROR_CONNECTION_FAILED;
            $this->error(messageKey: self::ERROR_CONNECTION_FAILED);
            return false;
        }

        if (!$json['success']) {
            if (!empty($json['error-codes'])) {
                foreach ($json['error-codes'] as $r) {
                    $this->errorCodes[] = $r;
                    $this->error(messageKey: $r);
                }
            } else {
                $this->errorCodes[] = 'no-error-codes';
                $this->error(messageKey: self::EXPIRED);
            }

            return false;
        }

        if ($json['challenge_ts']) {
            $challengeVerificationTimestamp = time();
            $challengeTime                  = strtotime(datetime: (string)$json['challenge_ts']);
            if ($challengeTime > 0 && ($challengeVerificationTimestamp - $challengeTime) > $this->responseTimeout) {
                $this->errorCodes[] = 'no-error-codes';
                $this->error(messageKey: self::EXPIRED);

                return false;
            }
        }

        return true;
    }

    public static function getIP(): ?string
    {
        $ipAddress = false;

        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ipAddress = $_SERVER["HTTP_CLIENT_IP"];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Put the IPs into an array which we shall work with shortly.
            $ips = explode(separator: ', ', string: (string)$_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ipAddress) {
                array_unshift($ips, $ipAddress);
                $ipAddress = false;
            }

            foreach ($ips as $iValue) {
                if (!preg_match(pattern: "#^(10|172\.16|192\.168)\.#", subject: (string)$iValue)) {
                    $ipAddress = $iValue;
                    break;
                }
            }
        }

        if (!$ipAddress && !isset($_SERVER['REMOTE_ADDR'])) {
            return null;
        }

        return $ipAddress ?: $_SERVER['REMOTE_ADDR'];
    }
}
