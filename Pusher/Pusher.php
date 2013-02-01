<?php

namespace Lopi\Bundle\PusherBundle\Pusher;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Pusher
{
    private $appId;
    private $key;
    private $secret;
    private $pathUrl;
    private $container;
    
    public function __construct($appId, $key, $secret, ContainerInterface $container)
    {
        $this->appId = $appId;
        $this->key = $key;
        $this->secret = $secret;
        $this->pathUrl = '/apps/' . $this->appId;
        $this->container = $container;
    }
    
    public function getKey()
    {
        return $this->key;
    }
    
    public function trigger($channelName, $eventName, $body, $socketId = null)
    {
        # Added channel in the URL
        $pathUrl = $this->pathUrl . '/channels/' . $channelName . '/events';

        if ($this->container->has('lopi_pusher.serializer')) {
            $serializer = $this->container->get('lopi_pusher.serializer');
            $bodyJson = $serializer->serialize($body, 'json');
        } else {
            $bodyJson = json_encode($body);
        }

        $query = 'auth_key=' . $this->key . 
            '&auth_timestamp=' . time() . 
            '&auth_version=' . $this->container->getParameter('lopi_pusher.auth.version')  . 
            '&body_md5=' . md5($bodyJson) . 
            '&name=' . $eventName;
            
        if ($socketId !== null) {
            $query .= '&socket_id=' . $socketId;
        }
        
        $authSignature = $this->authSignature($pathUrl, $query);
        $query .= '&auth_signature=' . $authSignature;
        
        if ( ($curlHandle = curl_init()) === false ) {
            throw new \Exception('Could not initialise cURL!' );
        }
        
        # Set cURL opts and execute request
        curl_setopt($curlHandle, CURLOPT_URL, $this->container->getParameter('lopi_pusher.host') . $pathUrl . '?' . $query );
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array ( "Content-Type: application/json" ) );
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($curlHandle, CURLOPT_POST, 1 );
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $bodyJson);
        
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        
        if ($response != "202 ACCEPTED\n") {
			$statusCode = substr($response, 0, 3);
			$message = $response . "\n" . 'Pusher Request rejected ' . $this->container->getParameter('lopi_pusher.host') . '/' . $pathUrl . '?' . $query;
			throw new HttpException($statusCode, $message);
        }
        
        return true;
    }
    
    protected function authSignature($pathUrl, $query)
    {
        $query = "POST\n" . $pathUrl . "\n" . $query;
        
        return hash_hmac( 'sha256', $query, $this->secret, false );
    }
}
