<?php
namespace Academicpuma\OAuth\Token;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\Message\ResponseInterface;

/**
 * Description of AccessToken
 *
 * @author sebastian
 */
class AccessToken implements TokenInterface {
    
    private $oauthToken;
    
    private $oauthTokenSecret;
    
    private $userId;
    
    public function __construct(ResponseInterface $response) {
        
        $params = array();
        parse_str((string) $response->getBody(), $params);
        
        $validParams = array_key_exists('oauth_token', $params)
                    && array_key_exists('oauth_token_secret', $params)
                    && array_key_exists('user_id', $params);
        
        if(!$validParams) {
            throw new \BadMethodCallException("Invalid params");
        }
        
        $this->oauthToken = $params['oauth_token'];
        $this->oauthTokenSecret = $params['oauth_token_secret'];
        $this->userId = $params['user_id'];
    }
    
    function getOauthToken() {
        return $this->oauthToken;
    }

    function getOauthTokenSecret() {
        return $this->oauthTokenSecret;
    }

    function getUserId() {
        return $this->userId;
    }
}
