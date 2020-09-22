<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle;

use Pusher\Pusher;

/**
 * @author Pierre-Louis Launay <laupi.frpar@gmail.com>
 */
class PusherFactory
{
    /**
     * @throws \Pusher\PusherException
     */
    public static function create(PusherConfiguration $configuration): Pusher
    {
        return new Pusher(
            $configuration->getAuthKey(),
            $configuration->getSecret(),
            $configuration->getAppId(),
            $configuration->getOptions()
        );
    }
}
