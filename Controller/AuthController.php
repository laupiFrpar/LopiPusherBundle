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

        if (strpos($channelName, 'presence') === 0) {
            $userData = json_encode(array(
                    'user_id'	=> $this->container->get('lopi_pusher.authenticator')->getUserId(),
                    'user_info' => $this->container->get('lopi_pusher.authenticator')->getUserInfo()
            ));
            $code = hash_hmac('sha256', $socketId.':'.$channelName.':'.$userData, $secret);
            $auth = $key.':'.$code;
            $responseData = array(
                'auth'          => $auth,
                'channel_data'  => $userData
            );
        } else {
            $code = hash_hmac('sha256', $socketId.':'.$channelName, $secret);
            $auth = $key.':'.$code;
            $responseData = array(
                'auth'          => $auth
            );
        }

        return new Response(json_encode($responseData), 200, array('Content-Type' => 'application/json'));
    }

}
