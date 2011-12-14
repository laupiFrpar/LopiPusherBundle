<?php
/*
 *
 */

namespace Lopi\Bundle\PusherBundle\Authenticator;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */ 
interface ChannelAuthenticatorInterface
{

    /**
     * @param string $socketId
     * @param string $channelName
     * @return bool
     */
    public function authenticate($socketId, $channelName);
}
