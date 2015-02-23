<?php
namespace Academicpuma\OAuth\Token;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\Message\ResponseInterface;

/**
 * Description of RequestToken
 *
 * @author sebastian
 */
class RequestToken implements TokenInterface {
    
    /**
     *
     * @var string token 
     */
    private $oauthToken;
    
    /**
     *
     * @var string token secret 
     */
    private $oauthTokenSecret;
    
    /**
     *
     * @var bool is callback confirmed?
     */
    private $oauthCallbackConfirmed;
    
    /**
     * 
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response) {
        
        $params = array();
        parse_str((string) $response->getBody(), $params);
        
        $validParams = array_key_exists('oauth_token', $params)
                    && array_key_exists('oauth_token_secret', $params)
                    && array_key_exists('oauth_callback_confirmed', $params);
               
        
        if(!$validParams) {
            throw new \BadMethodCallException("Invalid params");
        }
        
        $this->oauthToken               = (string) $params['oauth_token'];
        $this->oauthTokenSecret         = (string) $params['oauth_token_secret'];
        $this->oauthCallbackConfirmed   = (bool)   $params['oauth_callback_confirmed'];
    }
    
    function getOauthToken() {
        return $this->oauthToken;
    }

    function getOauthTokenSecret() {
        return $this->oauthTokenSecret;
    }

    function isOauthCallbackConfirmed() {
        return $this->oauthCallbackConfirmed;
    }


}
