<?php

namespace Lopi\Bundle\PusherBundle\Authenticator;

/**
 * ChannelAuthenticatorPresenceInterface
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
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
