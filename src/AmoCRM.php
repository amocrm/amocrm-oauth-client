<?php
namespace AmoCRM\OAuth2\Client\Provider;

use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class AmoCRM extends AbstractProvider
{
    /**
     * @var string
     */
    public $clientSubdomain = 'www';

    /**
     * @var string
     */
    public $protocol = 'https://';

    /**
     * @var string
     */
    public $clientTopLevelDomain = 'ru';

    /**
     * @var array
     */
    public $scopes = [];

    /**
     * @var string
     */
    public $authorizationHeader = 'Bearer';

    /**
     * @var array
     */
    public $headers = [
        'User-Agent' => 'amoCRM/oAuth Client 1.0'
    ];

    /**
     * AmoCRM constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        parent::__construct($options);

        if (isset($options['clientSubdomain'])) {
            $this->clientSubdomain = $options['clientSubdomain'];
        }

        if (isset($options['clientTopLevelDomain'])) {
            $this->clientTopLevelDomain = $options['clientTopLevelDomain'];
        }
    }

	public function urlAccount()
	{
		return $this->protocol . $this->clientSubdomain . '.amocrm.' . $this->clientTopLevelDomain . '/';
	}

    public function urlAuthorize()
    {
        return $this->protocol . 'www.amocrm.' . $this->clientTopLevelDomain . '/oauth/';
    }

    public function urlAccessToken()
    {
        return $this->protocol . $this->clientSubdomain . '.amocrm.' . $this->clientTopLevelDomain . '/oauth2/access_token';
    }

    public function urlUserDetails(AccessToken $token)
    {
        return $this->protocol . $this->clientSubdomain . '.amocrm.' . $this->clientTopLevelDomain . '/v3/user';
    }

    public function userDetails($response, AccessToken $token)
    {
        $user = new User();

        $user->exchangeArray([
            'uid'   => isset($response->id) ? $response->id : null,
            'name'  => isset($response->name) ? $response->name : null,
            'email' => isset($response->email) ? $response->email : null,
        ]);

        return $user;
    }


    public function getAuthorizationUrl($options = [])
    {
        $this->state = isset($options['state']) ? $options['state'] : md5(uniqid(rand(), true));

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $this->state,
        ];

        return $this->urlAuthorize().'?'.$this->httpBuildQuery($params, '', '&');
    }

}
