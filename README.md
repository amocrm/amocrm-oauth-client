# amoCRM Provider для OAuth 2.0 Client

В данном пакете представлена поддержка протокола OAuth 2.0 в amoCRM для библитеки PHP League [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Установка

Установить можно с помощью composer:

```
composer require amocrm/oauth2-amocrm
```

## Использование

Использование идентично использованию The League's OAuth client, используя `AmoCRM\OAuth2\Client\Provider\AmoCRM` в качестве провайдера.

### Обработка кода авторизации (Authorization Code)

```php
$provider = new AmoCRM\OAuth2\Client\Provider\AmoCRM([
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_CLIENT_SECRET',
    'redirectUri' => 'http://your-redirect-uri',
    'clientSubdomain' => 'example',
    'clientTopLevelDomain' => 'ru', //use com for us accounts
]);

if (isset($_GET['code']) && $_GET['code']) {
    $token = $this->provider->getAccessToken('authorizaton_code', [
        'code' => $_GET['code']
    ]);

    // Returns an instance of League\OAuth2\Client\User
    $user = $this->provider->getUserDetails($token);
    $uid = $provider->getUserUid($token);
    $email = $provider->getUserEmail($token);
    $screenName = $provider->getUserScreenName($token);
}
```

### Обновление access токена

```php
$provider = new AmoCRM\OAuth2\Client\Provider\AmoCRM([
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_CLIENT_SECRET',
    'redirectUri' => 'http://your-redirect-uri',
]);

$grant = new \League\OAuth2\Client\Grant\RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);
```

### Кнопка на сайт ###
Для удобства можно разместить кнопку на сайт для простой установки созданной интеграции.
```
<div class="button"></div>
<script>var AMOCRM_OAUTH = { client_id: "xxxxxx-client-id-xxxxxx", title: "Добавить в amoCRM" };</script>
<script id="amocrm_oauth" charset="utf-8" src="https://amocrm.ru/auth/button.js"></script>```
