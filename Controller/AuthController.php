<?php
/*
 *
 */

namespace Lopi\Bundle\PusherBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class AuthController extends ContainerAware
{

    /**
     * Implement http://pusher.com/docs/authenticating_users
     * and       http://pusher.com/docs/auth_signatures
     *
     * @param Request $request
     */
    public function authAction(Request $request)
    {
        $socketId = $request->get('socket_id');
        $channelName = $request->get('channel_name');

        if (!$this->container->get('lopi_pusher.authenticator')->authenticate($socketId, $channelName)) {
            throw new AccessDeniedException('Request authentication denied');
        }

        $secret = $this->container->getParameter('lopi_pusher.secret');
        $key = $this->container->getParameter('lopi_pusher.key');

        $code = hash_hmac('sha256', $socketId.":".$channelName, $secret);
        $auth = $key.":".$code;

        return new Response(json_encode(array('auth' => $auth)), 200, array('Content-Type' => 'application/json'));
    }

}
