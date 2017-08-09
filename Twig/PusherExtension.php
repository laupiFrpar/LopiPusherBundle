<?php

namespace Lopi\Bundle\PusherBundle\Twig;

use Pusher\Pusher;

class PusherExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $pusher;

    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    public function getGlobals()
    {
        return [
            'pusher_key' => $this->pusher->getSettings()['auth_key'],
        ];
    }
}
