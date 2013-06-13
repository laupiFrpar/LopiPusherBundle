<?php

namespace Lopi\Bundle\PusherBundle\Tests\DependencyInjection;

use Lopi\Bundle\PusherBundle\DependencyInjection\LopiPusherExtension;

use Pusher;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * PusherTest
 *
 * @author Pierre-Louis Launay <laupi.frpar@gmail.com>
 */
class PusherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the load of the configuration
     */
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

        $this->assertInstanceOf('Pusher', $container->get('lopi_pusher.pusher'));
        $this->assertEquals('app_id', $container->getParameter('lopi_pusher.app.id'));
        $this->assertEquals('key', $container->getParameter('lopi_pusher.key'));
        $this->assertEquals('secret', $container->getParameter('lopi_pusher.secret'));
        $this->assertFalse($container->getParameter('lopi_pusher.debug'));
        $this->assertEquals('http://api.pusherapp.com', $container->getParameter('lopi_pusher.host'));
        $this->assertEquals('80', $container->getParameter('lopi_pusher.port'));
        $this->assertEquals('30', $container->getParameter('lopi_pusher.timeout'));
        $this->assertEquals('acme_service_id', (string) $container->getAlias('lopi_pusher.authenticator'));
    }

    /**
     * Test the load of the configuration with custom config
     */
    public function testLoadWithConfig()
    {
        $container = new ContainerBuilder();
        $configs = array('lopi_pusher' => array(
            'app_id' => 'app_id',
            'key' => 'key',
            'secret' => 'secret',
            'debug' => true,
            'host' => 'https://api.pusherapp.com',
            'port' => '443',
            'timeout' => '60',
            'auth_service_id' => 'acme_service_id'
            ));
        $extension = new LopiPusherExtension();
        $extension->load($configs, $container);

        $this->assertInstanceOf('Pusher', $container->get('lopi_pusher.pusher'));
        $this->assertEquals('app_id', $container->getParameter('lopi_pusher.app.id'));
        $this->assertEquals('key', $container->getParameter('lopi_pusher.key'));
        $this->assertEquals('secret', $container->getParameter('lopi_pusher.secret'));
        $this->assertTrue($container->getParameter('lopi_pusher.debug'));
        $this->assertEquals('https://api.pusherapp.com', $container->getParameter('lopi_pusher.host'));
        $this->assertEquals('443', $container->getParameter('lopi_pusher.port'));
        $this->assertEquals('60', $container->getParameter('lopi_pusher.timeout'));
        $this->assertEquals('acme_service_id', (string) $container->getAlias('lopi_pusher.authenticator'));
    }
}
