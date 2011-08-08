<?php

namespace Lopi\Bundle\PusherBundle\Tests\Pusher;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Lopi\Bundle\PusherBundle\Pusher\Pusher;

class PusherTest extends \PHPUnit_Framework_TestCase
{
    public function testTrigger()
    {
        $container = new ContainerBuilder();
        $container->setParameter('lopi_pusher.host', 'http://api.pusherapp.com');
        $container->setParameter('lopi_pusher.auth.version', '1.0');
        $pusher = new Pusher('5818', '91ec2b8176b5473feafa', '3e39e8aac04bcf06ca78', $container);
        $this->assertTrue($pusher->trigger('test_channel', 'test_event', 'Test'));
    }
}