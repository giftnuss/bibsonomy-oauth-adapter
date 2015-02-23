<?php
namespace Academicpuma\OAuth;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\Client;

/**
 * Description of OAuthAdapter
 *
 * @author sebastian
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
     * @var \Academicpuma\OAuth\Subscriber\BibSonomySubscriber 
     */
    protected $bibsonomySubscriber;
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
    
    public function getConsumerToken() {
        
        return $this->consumerToken;
    }
    
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
     * @param \Academicpuma\OAuth\Token\RequestToken $requestToken
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
        
        throw new \BadMethodCallException("Error: The oauth_token from callback is not the same as the request_token");
    }
    
    /**
     * 
     * @param \Academicpuma\OAuth\Token\RequestToken $requestToken
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
     * @param \Academicpuma\OAuth\Token\AccessToken $accessToken
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
    
    
    public static function urlEncode($value){
        
        $encoded_ = rawurlencode($value);
        $encoded = str_replace('%7E', '~', $encoded_);
        return $encoded;
    }

}
