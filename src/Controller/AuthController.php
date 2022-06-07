<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\Controller;

use Exception;
use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface;
use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorPresenceInterface;
use Lopi\Bundle\PusherBundle\PusherConfiguration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuthController.
 *
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 * @author Pierre-Louis Launay <laupi.frpar@gmail.com>
 */
class AuthController
{
    public function __construct(
        private PusherConfiguration $configuration,
        private ChannelAuthenticatorInterface $authenticator
    ) {
    }

    /**
     * Implement http://pusher.com/docs/authenticating_users
     * and       http://pusher.com/docs/auth_signatures.
     */
    public function authAction(Request $request): Response
    {
        $socketId = $request->get('socket_id');

        $channelNames = $request->get('channel_name');
        if (\is_array($channelNames)) {
            $combineResponse = [];
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
     * Perform channel authentication.
     *
     * @param string $socketId    The socket id
     * @param string $channelName name of the channel to validate
     *
     * @return array|null response auth data or null on access denied
     *
     * @throws \JsonException
     */
    private function authenticateChannel(string $socketId, string $channelName): ?array
    {
        $responseData = [];
        $data = $socketId.':'.$channelName;

        if (!$this->authenticator->authenticate($socketId, $channelName)) {
            return null;
        }

        if ($this->authenticator instanceof ChannelAuthenticatorPresenceInterface && str_starts_with($channelName, 'presence')) {
            $responseData['channel_data'] = json_encode([
                'user_id' => $this->authenticator->getUserId(),
                'user_info' => $this->authenticator->getUserInfo(),
            ], \JSON_THROW_ON_ERROR);
            $data .= ':'.$responseData['channel_data'];
        }

        $responseData['auth'] = $this->configuration->getAuthKey().':'.$this->getCode($data);

        if (0 === strpos($channelName,'private-encrypted')) {
            $responseData['shared_secret'] = base64_encode($this->genSharedSecret($channelName));
        }

        return $responseData;
    }

    /**
     * Get the hashed data.
     *
     * @param string $data The data to hash
     */
    private function getCode(string $data): string
    {
        return hash_hmac('sha256', $data, $this->configuration->getSecret());
    }

    /**
     * Return shared secret derived from channel name and encryption master key.
     *
     * @param string $channel
     * @return string
     * @throws Exception
     */
    private function genSharedSecret(string $channel): string
    {
        if (!array_key_exists('encryption_master_key_base64', $this->configuration->getOptions())) {
            throw new Exception("Missing 'encryption_master_key_base64' from configuration options");
        }

        return hash('sha256', $channel . base64_decode($this->configuration->getOptions()['encryption_master_key_base64']), true);
    }
}
