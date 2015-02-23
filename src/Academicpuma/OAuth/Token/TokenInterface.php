<?php
namespace Academicpuma\OAuth\Token;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author sebastian
 */
interface TokenInterface {
    
    
    public function getOauthToken();
    
    public function getOauthTokenSecret();
}
