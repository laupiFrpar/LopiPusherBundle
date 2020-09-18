<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\Controller;

use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface;
use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorPresenceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * AuthController
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 * @author Pierre-Louis Launay <laupi.frpar@gmail.com>
 */
class AuthController
{
    private $pusherConfig;
    private $authenticator;

    public function __construnct(array $pusherConfig, ?ChannelAuthenticatorInterface $authenticator)
    {
        $this->pusherConfig = $pusherConfig;
        $this->authenticator = $authenticator;
    }

    /**
     * Implement http://pusher.com/docs/authenticating_users
     * and       http://pusher.com/docs/auth_signatures
     */
    public function authAction(Request $request): Response
    {
        if (!$this->authenticator) {
            throw new \Exception('The authenticator service does not exsit.');
        }

        $socketId = $request->get('socket_id');

        $channelNames = $request->get('channel_name');
        if (is_array($channelNames)) {
            $combineResponse = array();
            foreach ($channelNames as $channelName) {
                $responseData = $this->authenticateChannel($socketId, $channelName);

                if (!$responseData) {
                    $combineResponse[$channelName]['status'] = 403;

                    continue;
                }

                $combineResponse[$channelName]['status'] = 200;
                $combineResponse[$channelName]['data'] = $responseData;
            }

            return new JsonResponse($combineResponse);
        }

        $responseData = $this->authenticateChannel($socketId, $channelNames);
        if (!$responseData) {
            return new JsonResponse('Request authentication denied', 403);
        }

        return new JsonResponse($responseData);
    }

    /**
     * Perform channel autentication.
     *
     * @param string $socketId The socket id
     * @param string $channelName Name of the channel to validate.
     *
     * @return array Response auth data or null on access denied.
     */
    private function authenticateChannel(string $socketId, string $channelName): ?array
    {
        $responseData = array();
        $data = $socketId.':'.$channelName;

        if (!$this->authenticator->authenticate($socketId, $channelName)) {
            return null;
        }

        if (strpos($channelName, 'presence') === 0 && $this->authenticator instanceof ChannelAuthenticatorPresenceInterface) {
            $responseData['channel_data'] = \json_encode([
                'user_id' => $this->authenticator->getUserId(),
                'user_info' => $this->authenticator->getUserInfo(),
            ]);
            $data .= ':'.$responseData['channel_data'];
        }

        $responseData['auth'] = $this->pusherConfig['key'].':'.$this->getCode($data);

        return $responseData;
    }

    /**
     * Get the hashed data
     *
     * @param string $data The data to hash
     */
    private function getCode($data): string
    {
        return hash_hmac('sha256', $data, $this->pusherConfig['secret']);
    }
}
