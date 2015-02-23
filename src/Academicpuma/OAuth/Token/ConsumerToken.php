<?php
namespace Academicpuma\OAuth\Token;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of ConsumerToken
 *
 * @author sebastian
 */
class ConsumerToken implements TokenInterface {
    
    private $oauthToken;
    
    private $oauthTokenSecret;
    
    
    public function __construct($oauthToken, $oauthTokenSecret) {
        $this->oauthToken = $oauthToken;
        $this->oauthTokenSecret = $oauthTokenSecret;
    }
    
    public function getOauthToken() {
        return $this->oauthToken;
    }

    public function getOauthTokenSecret() {
        return $this->oauthTokenSecret;
    }

}
