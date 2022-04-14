<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\Authenticator;

/**
 * ChannelAuthenticatorInterface.
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface ChannelAuthenticatorInterface
{
    /**
     * @param string $socketId    The socket ID
     * @param string $channelName The channel name
     */
    public function authenticate(string $socketId, string $channelName): bool;
}
