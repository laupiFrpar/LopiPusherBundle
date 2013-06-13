<?php

namespace Lopi\Bundle\PusherBundle\Authenticator;

/**
 * ChannelAuthenticatorInterface
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface ChannelAuthenticatorInterface
{

    /**
     * @param string $socketId    The socket ID
     * @param string $channelName The channel name
     *
     * @return bool
     */
    public function authenticate($socketId, $channelName);
}
