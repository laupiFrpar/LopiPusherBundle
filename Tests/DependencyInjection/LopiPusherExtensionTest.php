<?php

namespace Lopi\Bundle\PusherBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Lopi\Bundle\PusherBundle\DependencyInjection\LopiPusherExtension;
use Lopi\Bundle\PusherBundle\Pusher\Pusher;

class PusherTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
		$container = new ContainerBuilder();
        $configs = array('lopi_pusher' => array(
			'app_id' => 'app_id',
			'key' => 'key',
			'secret' => 'secret'
			));
		$expectedPusher = new Pusher('app_id', 'key', 'secret', $container);
		$extension = new LopiPusherExtension();
		$extension->load($configs, $container);
		
		$this->assertEquals($container->get('lopi_pusher.pusher'), $expectedPusher);
    }
}