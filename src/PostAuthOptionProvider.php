<?php

namespace AmoCRM\OAuth2\Client\Provider;

use League\OAuth2\Client\OptionProvider\OptionProviderInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Tool\QueryBuilderTrait;

class PostAuthOptionProvider implements OptionProviderInterface
{
    use QueryBuilderTrait;

    /**
     * @inheritdoc
     */
    public function getAccessTokenOptions($method, array $params)
    {
        $options = ['headers' => ['Content-Type' => 'application/json']];

        if ($method === AbstractProvider::METHOD_POST) {
            $options['body'] = json_encode($params, JSON_UNESCAPED_SLASHES);
        }

        return $options;
    }
}
