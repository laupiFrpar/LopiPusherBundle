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
			'secret' => 'secret',
            'auth_service_id' => 'acme_service_id'
			));
		$extension = new LopiPusherExtension();
		$extension->load($configs, $container);
		$expectedPusher = new Pusher('app_id', 'key', 'secret', $container);
		$this->assertEquals($expectedPusher, $container->get('lopi_pusher.pusher'));
		$this->assertEquals('http://api.pusherapp.com', $container->getParameter('lopi_pusher.host'));
		$this->assertEquals('app_id', $container->getParameter('lopi_pusher.app.id'));
		$this->assertEquals('key', $container->getParameter('lopi_pusher.key'));
		$this->assertEquals('secret', $container->getParameter('lopi_pusher.secret'));
		$this->assertTrue(is_string($container->getParameter('lopi_pusher.auth.version')));
		$this->assertTrue("1.0" === $container->getParameter('lopi_pusher.auth.version'));
		$this->assertFalse($container->getParameter('lopi_pusher.encrypted'));
        $this->assertEquals('acme_service_id', (string) $container->getAlias('lopi_pusher.authenticator'));
    }
	
	public function testLoadWithConfig()
    {
		$container = new ContainerBuilder();
        $configs = array('lopi_pusher' => array(
			'app_id' => 'app_id',
			'key' => 'key',
			'secret' => 'secret',
			'encrypted' => true,
            'auth_service_id' => 'acme_service_id'
			));
		$extension = new LopiPusherExtension();
		$extension->load($configs, $container);
		$expectedPusher = new Pusher('app_id', 'key', 'secret', $container);
		$this->assertEquals($expectedPusher, $container->get('lopi_pusher.pusher'));
		$this->assertEquals('app_id', $container->getParameter('lopi_pusher.app.id'));
		$this->assertEquals('key', $container->getParameter('lopi_pusher.key'));
		$this->assertEquals('secret', $container->getParameter('lopi_pusher.secret'));
		$this->assertEquals('http://api.pusherapp.com', $container->getParameter('lopi_pusher.host'));
		$this->assertTrue(is_string($container->getParameter('lopi_pusher.auth.version')));
		$this->assertTrue("1.0" === $container->getParameter('lopi_pusher.auth.version'));
		$this->assertTrue($container->getParameter('lopi_pusher.encrypted'));
        $this->assertEquals('acme_service_id', (string) $container->getAlias('lopi_pusher.authenticator'));
    }
}