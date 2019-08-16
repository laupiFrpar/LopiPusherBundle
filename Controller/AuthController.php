<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     *
     * @return Response
     *
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function authAction(Request $request)
    {
        if (!$this->container->has('lopi_pusher.authenticator')) {
            throw new \Exception('The authenticator service does not exsit.');
        }

        $authenticator = $this->container->get('lopi_pusher.authenticator');
        $socketId = $request->get('socket_id');

        $channelNames = $request->get('channel_name');
        if (is_array($channelNames)) {
            $combineResponse = array();
            foreach ($channelNames as $channelName) {
                $responseData = $this->authenticateChannel($socketId, $channelName, $authenticator);
            
                if (!$responseData) {
                    $combineResponse[$channelName]['status'] = 403;

                    continue;
                }

                $combineResponse[$channelName]['status'] = 200;
                $combineResponse[$channelName]['data'] = $responseData;
            }

            return new Response(json_encode($combineResponse), 200, array('Content-Type' => 'application/json'));
        }

        $responseData = $this->authenticateChannel($socketId, $channelNames, $authenticator);
        if (!$responseData) {
            throw new AccessDeniedException('Request authentication denied');
        }

        return new Response(json_encode($responseData), 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Perform channel autentication.
     * 
     * @param string $socketId The socket id
     * @param string $channelName Name of the channel to validate. 
     *
     * @return array Response auth data or null on access denied.
     */
    private function authenticateChannel($socketId, $channelName, $authenticator): ?array
    {
        $responseData = array();
        $data = $socketId.':'.$channelName;

        if (!$authenticator->authenticate($socketId, $channelName)) {
            return null;
        }

        if (strpos($channelName, 'presence') === 0 && $authenticator instanceof ChannelAuthenticatorPresenceInterface) {
            $responseData['channel_data'] = json_encode([
                'user_id' => $authenticator->getUserId(),
                'user_info' => $authenticator->getUserInfo(),
            ]);
            $data .= ':'.$responseData['channel_data'];
        }

        $responseData['auth'] = $this->container->getParameter('lopi_pusher.config')['key'].':'.$this->getCode($data);

        return $responseData;
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
