<?php

declare(strict_types=1);

namespace Test\Behaviour\Context\Lib;

use Assert\Assert;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Curl\Curl;


trait AuthTrait
{
    use CurlTrait;

    protected string $accessToken;
    protected string $refreshToken;
    protected string $authRealm;
    protected string $authClientId;
    protected string $authClientSecret;
    protected string $authTestRole;
    protected string $authTestUserName;
    protected string $authTestUserPassword;
    protected string $authUrlPrefix;
    protected string $authAdminUserName;
    protected string $authAdminPassword;
    protected string $authOtherTestUserName;
    protected string $authOtherTestUserPassword;
    protected bool $userAuthenticated;

    /**
     * @BeforeScenario @auth
     */
    public function beforeAuth(BeforeScenarioScope $scope)
    {
        $this->setupAuth();
    }

    public function setupAuth()
    {
        $this->authRealm = getenv('AUTH_REALM');
        $this->authClientId = getenv('AUTH_CLIENT_ID');
        $this->authClientSecret = getenv('AUTH_CLIENT_SECRET');
        $this->authUrlPrefix = (getenv('AUTH_PROTOCOL') ?: 'http') . '://'
            . getenv('AUTH_HOSTNAME')
            . (getenv('AUTH_PORT') ? ':' . getenv('AUTH_PORT') : '');
        $this->authTestRole = 'testrole';
        $this->authTestUserName = 'testuser';
        $this->authTestUserPassword = 'password';
        $this->authOtherTestUserName = 'secondtestuser';
        $this->authOtherTestUserPassword = 'password';
        $this->authAdminUserName = 'admin';
        $this->authAdminPassword = 'admin';

        $this->clearCapabilities();

        echo "Accessing auth via: " . $this->authUrlPrefix . "\n";
        echo "Auth Admin user name: " . $this->authAdminUserName . "\n";
        echo "Realm: " . $this->authRealm . "\n";
        echo "ClientId: " . $this->authClientId . "\n";
        echo "Test role: " . $this->authTestRole . "\n";
        echo "Test user name: " . $this->authTestUserName;
    }

    protected function getAuthAdminAccessToken(string $realm, string $clientId, string $clientSecret, string $username, string $password)
    {
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->post($this->authUrlPrefix . '/auth/realms/' . $realm . '/protocol/openid-connect/token', [
            'username' => $username,
            'password' => $password,
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ]);
        $response = $curl->response;
        $this->accessToken = json_decode($response, true)['access_token'];
        $this->refreshToken = json_decode($response, true)['refresh_token'];
    }

    protected function authCurl(string $realm, string $clientId, string $clientSecret, string $username, string $password, Curl $curl = null)
    {
        $this->getAuthAdminAccessToken($realm, $clientId, $clientSecret, $username, $password);
        if (!$curl) {
            $curl = $this->curl;
        }
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Connection', 'close');
        $curl->setHeader('Authorization', 'Bearer ' . $this->accessToken);
    }

    protected function authTestUser(?string $username = null, ?string $password = null)
    {
        if (!$username) {
            $username = $this->authTestUserName;
        }
        if (!$password) {
            $password = $this->authTestUserPassword;
        }
        $this->authCurl($this->authRealm, $this->authClientId, $this->authClientSecret, $username, $password);
    }

    protected function authOtherTestUser()
    {
        $this->authTestUser($this->authOtherTestUserName, $this->authOtherTestUserPassword);
    }

    protected function addTestRoleCapability(string $capabilityRoleId)
    {
        $curl = new Curl();
        $this->authCurl('master', 'admin-cli', '', $this->authAdminUserName, $this->authAdminPassword, $curl);
        $curl->post($this->authUrlPrefix . '/auth/admin/realms/' . $this->authRealm . '/clients/' . $this->authClientId . '/roles/' . $this->authTestRole . '/composites', [
            [
                'id' => $capabilityRoleId,
            ]
        ], true);
        Assert::that($curl->error)->noContent();
    }

    protected function removeTestRoleCapability(string $capabilityRoleId)
    {
        $curl = new Curl();
        $url = $this->authUrlPrefix . '/auth/admin/realms/' . $this->authRealm . '/clients/' . $this->authClientId . '/roles/' . $this->authTestRole . '/composites/';
        $this->authCurl('master', 'admin-cli', '', $this->authAdminUserName, $this->authAdminPassword, $curl);
        $curl->get($url);

        $existing = array_values(
            array_filter(
                json_decode($curl->response, true),
                fn (array $capabilityRole) => $capabilityRole['id'] == $capabilityRoleId
            )
        );

        if ($existing) {
            $curl->delete($url, json_encode($existing), true);
        }
    }

    protected function clearCapabilities()
    {
        $curl = new Curl();
        $url = $this->authUrlPrefix . '/auth/admin/realms/' . $this->authRealm . '/clients/' . $this->authClientId . '/roles/' . $this->authTestRole . '/composites';
        $this->authCurl('master', 'admin-cli', '', $this->authAdminUserName, $this->authAdminPassword, $curl);
        $curl->get($url);
        if (!empty($curl->response)) {
            $curl->delete($url, $this->curl->response, true);
        }
    }

    protected function introspectAccessToken()
    {
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->setBasicAuthentication($this->authClientId, $this->authClientSecret);
        $curl->post($this->authUrlPrefix . '/auth/realms/' . $this->authRealm . '/protocol/openid-connect/token/introspect', [
            'token' => $this->accessToken
        ]);
    }

    /**
     * @Given the user has the :arg1 capability
     */
    public function theUserHasTheCapability($arg1)
    {
        $this->addTestRoleCapability($arg1);
    }

    public function otherUserHasTheCapability($arg1)
    {
        $this->addTestRoleCapability($arg1);
    }

    /**
     * @Given the user is authenticated
     */
    public function theUserIsAuthenticated()
    {
        $this->authTestUser();
        $this->userAuthenticated = true;
    }

    /**
     * @Given the user is not authenticated
     */
    public function theUserIsNotAuthenticated()
    {
        $this->newCurl();
        $this->userAuthenticated = false;
    }

    /**
     * @Given the user has not the :arg1 capability
     */
    public function theUserHasNotTheCapability($arg1)
    {
        $this->removeTestRoleCapability($arg1);
    }
}
