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
$provider = new AmoCRM([
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_CLIENT_SECRET',
    'redirectUri' => 'http://your-redirect-uri',
]);

if (isset($_GET['code']) && $_GET['code']) {
    $token = $this->provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    //Вызов функции setBaseDomain требуется для установки контектс аккаунта.
    if (isset($_GET['referer'])) {
        $provider->setBaseDomain($_GET['referer']);
    }
    
    //todo сохраняем access, refresh токены и привязку к аккаунту и возможно пользователю

	/** @var \AmoCRM\OAuth2\Client\Provider\AmoCRMResourceOwner $ownerDetails */
    $ownerDetails = $provider->getResourceOwner($token);

    printf('Hello, %s!', $ownerDetails->getName());
}
```

### Обновление access токена

```php
$provider = new AmoCRM([
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_CLIENT_SECRET',
    'redirectUri' => 'http://your-redirect-uri',
]);

//todo получение токена из хранилища

$provider->setBaseDomain($token['baseDomain']);
/**
 * Проверяем активен ли токен и делаем запрос или обновляем токен
 */
if (time() >= $token['expires']) {
	/**
	 * Получаем токен по рефрешу
	 */
	try {
		$accessToken = $provider->getAccessToken(new League\OAuth2\Client\Grant\RefreshToken(), [
			'refresh_token' => $token['refreshToken'],
		]);

		//todo сохраняем новые access, refresh токены и привязку к аккаунту и возможно пользователю

	} catch (Exception $e) {
		die((string)$e);
	}
}

//todo повторяем исходный запрос
```

### Кнопка на сайт ###
Для удобства можно разместить кнопку на сайт для простой установки созданной интеграции.
```
<div class="button"></div>
<script 
    id="amocrm_oauth"
    charset="utf-8"
    data-client-id="xxxxxx-client-id-xxxxxx"
    data-title="Добавить в amoCRM"
    data-compact="false"
    data-class-name="className"
    data-color="red"
    data-state="random string"
    src="https://www.amocrm.ru/auth/button.js"
></script>
```

### Пример
В рамках данного репозитория имеется файл **example.php**, который реализует простейшую логику авторизации, сохранения токена, а также совершения запросов.
Для использования нужно указать корректные значения при создании провайдера в файле, а также Redirect URI в интеграции ведущий на вызов этого файла на вашем сервере.
Дальше для теста можно перейти на страницу example.php, после чего будет отображена кнопка для открытия модального окна автооризации приложения в amoCRM.
После получения доступов вы увидете имя пользователя на экране.
Если добавить GET параметр - request=1, то будет совершен запрос за информацией об аккаунте с сохраненным ранее токеном.
