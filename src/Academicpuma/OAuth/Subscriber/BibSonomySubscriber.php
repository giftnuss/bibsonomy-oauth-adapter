<?php
namespace Academicpuma\OAuth\Subscriber;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Academicpuma\OAuth\Token\ConsumerToken;
use Academicpuma\OAuth\Token\AccessToken;
use Academicpuma\OAuth\Token\RequestToken;
/**
 * Description of OAuth
 *
 * @author sebastian
 */
class BibSonomySubscriber extends Oauth1 {

    /**
     *
     * @var Academicpuma\OAuth\Token\ConsumerToken  
     */
    protected $consumerToken;
    
    
    /**
     * 
     * @param ConsumerToken $consumerToken
     */
    public function __construct(ConsumerToken $consumerToken) {
        parent::__construct([]);
        $this->consumerToken = $consumerToken;
    }
    
    /**
     * 
     * @return GuzzleHttp\Subscriber\Oauth\Oauth1
     */
    public function getRequestTokenSubscriber() {
        return new parent([
            'consumer_key'    => $this->consumerToken->getOauthToken(),
            'consumer_secret' => $this->consumerToken->getOauthTokenSecret()
        ]);
    }

    /**
     * 
     * @param RequestToken $requestToken
     * @return GuzzleHttp\Subscriber\Oauth\Oauth1
     */
    public function getAccessTokenSubscriber(RequestToken $requestToken) {
        
        return new parent([
            'consumer_key'    => $this->consumerToken->getOauthToken(),
            'consumer_secret' => $this->consumerToken->getOauthTokenSecret(),
            'token'           => $requestToken->getOauthToken(),
            'token_secret'    => $requestToken->getOauthTokenSecret()
        ]);
    }

    /**
     * 
     * @param AccessToken $accessToken
     * @return GuzzleHttp\Subscriber\Oauth\Oauth1
     */
    public function getOAuthSubscriber(AccessToken $accessToken) {
        
        return new parent([
            'consumer_key'    => $this->consumerToken->getOauthToken(),
            'consumer_secret' => $this->consumerToken->getOauthTokenSecret(),
            'token'           => $accessToken->getOauthToken(),
            'token_secret'    => $accessToken->getOauthTokenSecret()
        ]);
    }
}
