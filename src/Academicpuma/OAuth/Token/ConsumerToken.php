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

namespace Academicpuma\OAuth\Token;

/**
 * This class represents the ConsumerToken
 *
 * @author Sebastian Böttger <boettger@cs.uni-kassel.de>
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
