<?php

namespace AmoCRM\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class AmoCRMException extends IdentityProviderException
{
    public static function errorResponse(ResponseInterface $response, $data)
    {
        $message = 'Error happen';

        if (!empty($data['title'])) {
            $message = $data['title'];
        }

        if (!empty($data['detail'])) {
            $message .= ': ' . $data['detail'];
        }

        if (!empty($data['hint'])) {
            $message .= " ({$data['hint']})";
        }

        $code = 0;
        if (isset($data['error_code'])) {
            $code = $data['error_code'];
        }

        $body = (string)$response->getBody();
        return new static($message, $code, $body);
    }
}
