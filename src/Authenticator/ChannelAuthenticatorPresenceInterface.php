<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\Authenticator;

/**
 * ChannelAuthenticatorPresenceInterface
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
interface ChannelAuthenticatorPresenceInterface extends ChannelAuthenticatorInterface
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
     * @return string
     */
    public function getUserId();
}
