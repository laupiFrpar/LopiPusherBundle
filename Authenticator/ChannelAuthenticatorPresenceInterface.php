<?php
/*
 *
 */

namespace Lopi\Bundle\PusherBundle\Authenticator;

interface ChannelAuthenticatorPresenceInterface
{
    /**
     * Returns an optional array of user info
     * 
     * @return array
     */
    public function getUserInfo();
    
    /**
     * Return the user id when authenticated, used for presence channels
     * 
     * @returns string
     */
    public function getUserId();
}
