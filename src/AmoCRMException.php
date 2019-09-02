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

        $code = $response->getStatusCode();
        $body = (string)$response->getBody();
        return new static($message, $code, $body);
    }
}