<?php

/* 
    Copyright (C) 2015 - Sebastian Böttger <boettger@cs.uni-kassel.de>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace AcademicPuma\OAuth;
use GuzzleHttp\Client;

/**
 * 
 *
 * @author Sebastian Böttger <boettger@cs.uni-kassel.de>
 */
class OAuthAdapter {

    
    const REQUEST_TOKEN_URL = 'oauth/requestToken';
    
    const ACCESS_TOKEN_URL = 'oauth/accessToken';
    
    const AUTHORIZE_URL = 'oauth/authorize';
    
    
    public static $CLIENT_METHODS = ['get', 'post', 'put', 'delete', 'head', 'options', 'patch'];
    
    /**
     *
     * @var array
     */
    protected $config;
    
    /**
     *
     * @var Token\ConsumerToken
     */
    protected $consumerToken;
    
    /**
     *
     * @var \GuzzleHttp\Client 
     */
    protected $client;
    
    /**
     *
     * @var \AcademicPuma\OAuth\Subscriber\BibSonomySubscriber 
     */
    protected $bibsonomySubscriber;
    
    /**
     * 
     * @var array
     */
    protected $_parameters;
    
    /**
     * 
     * @param array $config <code>['consumerKey' => '','consumerSecret => '', 'callbackUrl' => '', 'baseUrl' => '']</code>
     */
    public function __construct(array $config = []) {
        
        $this->config = $config;
        
        $this->client = new Client(['base_url' => $config['baseUrl'], 'defaults' => ['auth' => 'oauth']]);
        
        $this->consumerToken = new Token\ConsumerToken($config['consumerKey'], $config['consumerSecret']);
        
        $this->bibsonomySubscriber = new Subscriber\BibSonomySubscriber($this->consumerToken);
    }
    
    /**
     * 
     * @return \AcademicPuma\OAuth\Token\ConsumerToken
     */
    public function getConsumerToken() {
        
        return $this->consumerToken;
    }
    
    /**
     * 
     * @return \AcademicPuma\OAuth\Token\RequestToken
     */
    public function getRequestToken() {
        
        $this->client->getEmitter()->attach($this->bibsonomySubscriber->getRequestTokenSubscriber());
        
        $res = $this->client->post(
                self::REQUEST_TOKEN_URL,
                ['body' => ['oauth_callback' => $this->config['callbackUrl']]]
        );
        
        return new Token\RequestToken($res);
    }

    /**
     *
     * @param \AcademicPuma\OAuth\Token\RequestToken $requestToken
     *
     * @return Token\AccessToken
     */
    public function getAccessToken(Token\RequestToken $requestToken) {
        
        $authToken = filter_input(INPUT_GET, 'oauth_token', FILTER_SANITIZE_STRING);
        $userId    = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_STRING);
        
        if($requestToken->getOauthToken() === $authToken) {
            $subscriber = $this->bibsonomySubscriber->getAccessTokenSubscriber($requestToken);
            $this->client->getEmitter()->attach($subscriber);
            $res = $this->client->post(self::ACCESS_TOKEN_URL, ['body' => ['user_id' => $userId]]);
            
            $accessToken = new Token\AccessToken($res);
            $this->client->getEmitter()->detach($subscriber);
            
            return $accessToken;
        }
        
        throw new \InvalidArgumentException("Error: The oauth_token from callback is not the same as the request_token");
    }
    
    /**
     * 
     * @param \AcademicPuma\OAuth\Token\RequestToken $requestToken
     */
    public function redirect(Token\RequestToken $requestToken) {
            
        $params_ = $this->assembleRedirectParams($requestToken);
        $encodedParams = array();
        foreach ($params_ as $key => $value) {
            $encodedParams[] = self::urlEncode($key)
                             . '='
                             . self::urlEncode($value);
        }
        $params = implode('&', $encodedParams);
        
        header('Location: ' . $this->buildAuthorizeUrl($params));
    }
    
    private function buildAuthorizeUrl($params) {
        
        return $this->config['baseUrl'] . self::AUTHORIZE_URL . '?' . $params;
    }
    
    private function assembleRedirectParams(Token\RequestToken $requestToken) {
        $params = array(
            'oauth_token' => $requestToken->getOauthToken()
        );

        $params['oauth_callback'] = $this->config['callbackUrl'];
        
        if (!empty($this->_parameters)) {
            $params = array_merge($params, $this->_parameters);
        }

        return $params;
    }

    /**
     * Attaches $accessToken to the emitter
     *
     * @param \AcademicPuma\OAuth\Token\AccessToken $accessToken
     *
     * @return Client
     */
    public function prepareClientForOAuthRequests(Token\AccessToken $accessToken) {
        $this->client = new Client([
            'base_url' => $this->config['baseUrl'] . 'api', 
            'defaults' => ['auth' => 'oauth']
        ]);
        
        $this->client->getEmitter()->attach($this->bibsonomySubscriber->getOAuthSubscriber($accessToken));
        return $this->client;
    }
    
    public function __call($method, array $args = []) {
        
        if (!in_array($method, self::$CLIENT_METHODS)) {
            throw new \BadMethodCallException("Method $method does not exist on " . __CLASS__);
        }
        
        $url = isset($args[0]) ? $args[0] : null;
        $options = isset($args[1]) ? $args[1] : [];
        
        // trigger request
        return $this->client->$method($url, $options);
    }
    
    /**
     * 
     * @param string $value
     * @return string
     */
    public static function urlEncode($value){
        
        $encoded_ = rawurlencode($value);
        $encoded = str_replace('%7E', '~', $encoded_);
        return $encoded;
    }

}
