<?php
define('TOKEN_FILE', __DIR__ . DIRECTORY_SEPARATOR . 'token_info.json');

use AmoCRM\OAuth2\Client\Provider\AmoCRM;

include_once 'vendor/autoload.php';
include_once 'src/AmoCRM.php';

session_start();
/**
 * Создаем провайдера
 */
$provider = new AmoCRM([
    'clientId' => 'xxx',
    'clientSecret' => 'xxx',
    'redirectUri' => '',
    'clientSubdomain' => 'xxx',
    'clientTopLevelDomain' => 'ru', //use com for us accounts
]);

if (!isset($_GET['request'])) {
	if (!isset($_GET['code'])) {
		/**
		 * Получаем ссылку для авторизации и дальше редиректим
		 */
		$authorizationUrl = $provider->getAuthorizationUrl();
		$_SESSION['oauth2state'] = $provider->state;
		header('Location: ' . $authorizationUrl);
	} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
		unset($_SESSION['oauth2state']);
		exit('Invalid state');
	}

	/**
	 * Ловим обратный код
	 */
	try {
		/** @var \League\OAuth2\Client\Token\AccessToken $access_token */
		$accessToken = $provider->getAccessToken(new League\OAuth2\Client\Grant\AuthorizationCode(), [
			'code' => $_GET['code'],
		]);
		if (isset($accessToken) &&
			isset($accessToken->accessToken) &&
			isset($accessToken->refreshToken) &&
			isset($accessToken->expires)
		) {
			saveToken($accessToken);
		}
	} catch (Exception $e) {
		die((string)$e);
	}

	/**
	 * Получаем данные о пользователе
	 */
	$user = $provider->getUserDetails($accessToken);

	var_dump($user);
} else {
	$accessToken = getToken();
	/**
	 * Проверяем активен ли токен и делаем запрос или обновляем токен
	 */
	if (time() >= $accessToken->expires) {
		/**
		 * Получаем токен по рефрешу
		 */
		try {
			$accessToken = $provider->getAccessToken(new League\OAuth2\Client\Grant\RefreshToken(), [
				'refresh_token' => $accessToken->refreshToken,
			]);

			saveToken($accessToken);

		} catch (Exception $e) {
			die((string)$e);
		}
	}


	try {
		/**
		 * Делаем запрос к АПИ
		 */
		$data = $provider->getHttpClient()
			->createRequest('GET', $provider->urlAccount() . 'api/v2/account', $provider->getHeaders($accessToken->accessToken), null, ['debug' => true])
			->send();

		$parsedBody = json_decode($data->getBody(true), true);

		var_dump($parsedBody);
	} catch (Guzzle\Http\Exception\ClientErrorResponseException $e) {
		var_dump((string)$e);
	}
}


function saveToken($accessToken) {
	if (isset($accessToken) &&
		isset($accessToken->accessToken) &&
		isset($accessToken->refreshToken) &&
		isset($accessToken->expires)
	) {
		$data = [
			'accessToken' => $accessToken->accessToken,
			'expires' => $accessToken->expires,
			'refreshToken' => $accessToken->refreshToken,
		];

		file_put_contents(TOKEN_FILE, json_encode($data));
	} else {
		exit('Invalid access token ' . var_export($accessToken, true));
	}
}


function getToken() {
	$accessToken = json_decode(file_get_contents(TOKEN_FILE));

	if (isset($accessToken) &&
		isset($accessToken->accessToken) &&
		isset($accessToken->refreshToken) &&
		isset($accessToken->expires)
	) {
		return $accessToken;
	} else {
		exit('Invalid access token ' . var_export($accessToken, true));
	}
}