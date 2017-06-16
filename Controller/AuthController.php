<?php

namespace Lopi\Bundle\PusherBundle\Controller;

use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorPresenceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * AuthController
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 * @author Pierre-Louis Launay <laupi.frpar@gmail.com>
 */
class AuthController extends Controller
{

    /**
     * Implement http://pusher.com/docs/authenticating_users
     * and       http://pusher.com/docs/auth_signatures
     *
     * @param Request $request
     * @return Response
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function authAction(Request $request)
    {
        if (!$this->container->has('lopi_pusher.authenticator')) {
            throw new \Exception('The authenticator service does not exsit.');
        }

        $responseData = array();
        $authenticator = $this->container->get('lopi_pusher.authenticator');
        $socketId = $request->get('socket_id');
        $channelName = $request->get('channel_name');
        $data = $socketId . ':' . $channelName;

        if (!$authenticator->authenticate($socketId, $channelName)) {
            throw new AccessDeniedException('Request authentication denied');
        }

        if (strpos($channelName, 'presence') === 0 && $authenticator instanceof ChannelAuthenticatorPresenceInterface) {
            $responseData['channel_data'] = json_encode(array('user_id' => $authenticator->getUserId(), 'user_info' => $authenticator->getUserInfo()));
            $data .= ':' . $responseData['channel_data'];
        }

        $responseData['auth'] = $this->container->getParameter('lopi_pusher.config')['key'] . ':' . $this->getCode($data);

        return new Response(json_encode($responseData), 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Get the hashed data
     *
     * @param string $data The data to hash
     *
     * @return string
     */
    private function getCode($data)
    {
        return hash_hmac('sha256', $data, $this->container->getParameter('lopi_pusher.config')['secret']);
    }
}
