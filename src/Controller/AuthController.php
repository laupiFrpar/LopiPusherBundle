<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\Controller;

use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorPresenceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * AuthController
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 * @author Pierre-Louis Launay <laupi.frpar@gmail.com>
 */
class AuthController extends AbstractController
{

    /**
     * Implement http://pusher.com/docs/authenticating_users
     * and       http://pusher.com/docs/auth_signatures
     *
     * @param Request $request
     *
     * @return Response
     */
    public function authAction(Request $request)
    {
        if (!$this->has('lopi_pusher.authenticator')) {
            throw new \Exception('The authenticator service does not exsit.');
        }

        $authenticator = $this->get('lopi_pusher.authenticator');
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

            return $this->json($combineResponse);
        }

        $responseData = $this->authenticateChannel($socketId, $channelNames, $authenticator);
        if (!$responseData) {
            return $this->json('Request authentication denied', 403);
        }

        return $this->json($responseData);
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

        $responseData['auth'] = $this->getParameter('lopi_pusher.config')['key'].':'.$this->getCode($data);

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
        return hash_hmac('sha256', $data, $this->getParameter('lopi_pusher.config')['secret']);
    }
}
