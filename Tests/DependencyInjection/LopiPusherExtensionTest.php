<?php

namespace Lopi\Bundle\PusherBundle\Tests\DependencyInjection;

use Lopi\Bundle\PusherBundle\DependencyInjection\LopiPusherExtension;
use Pusher\Pusher;
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
        $configs = [
            'lopi_pusher' => [
                'app_id' => 'app_id',
                'key' => 'key',
                'secret' => 'secret',
                'auth_service_id' => 'acme_service_id',
            ],
        ];
        $extension = new LopiPusherExtension();
        $extension->load($configs, $container);

        $this->assertInstanceOf(Pusher::class, $container->get('lopi_pusher.pusher'));
        $this->assertEquals('app_id', $container->getParameter('lopi_pusher.config')['app_id']);
        $this->assertEquals('key', $container->getParameter('lopi_pusher.config')['key']);
        $this->assertEquals('secret', $container->getParameter('lopi_pusher.config')['secret']);
        $this->assertEquals('acme_service_id', $container->getParameter('lopi_pusher.config')['auth_service_id']);
        $this->assertFalse($container->getParameter('lopi_pusher.config')['debug']);
        $this->assertEquals('http', $container->getParameter('lopi_pusher.config')['scheme']);
        $this->assertEquals('api.pusherapp.com', $container->getParameter('lopi_pusher.config')['host']);
        $this->assertEquals('80', $container->getParameter('lopi_pusher.config')['port']);
        $this->assertEquals('30', $container->getParameter('lopi_pusher.config')['timeout']);
        $this->assertEquals('acme_service_id', (string) $container->getAlias('lopi_pusher.authenticator'));
    }

    /**
     * Test the load of the configuration with custom config
     */
    public function testLoadWithConfig()
    {
        $container = new ContainerBuilder();
        $configs = [
            'lopi_pusher' => [
                'url' => 'http://key:secret@api-eu.pusher.com/apps/app_id',
                'cluster' => 'cluster',
                'debug' => true,
                'port' => '443',
                'timeout' => '60',
                'auth_service_id' => 'acme_service_id',
            ],
        ];
        $extension = new LopiPusherExtension();
        $extension->load($configs, $container);

        $pusher = $container->get('lopi_pusher.pusher');
        $pusherSettings = $pusher->getSettings();

        $this->assertInstanceOf(Pusher::class, $pusher);
        $this->assertEquals('app_id', $pusherSettings['app_id']);
        $this->assertEquals('key', $pusherSettings['auth_key']);
        $this->assertEquals('secret', $pusherSettings['secret']);
        $this->assertEquals('cluster', $container->getParameter('lopi_pusher.config')['cluster']);
        $this->assertTrue($pusherSettings['debug']);
        $this->assertEquals('api-eu.pusher.com', $pusherSettings['host']);
        $this->assertEquals('443', $pusherSettings['port']);
        $this->assertEquals('60', $pusherSettings['timeout']);
        $this->assertEquals('acme_service_id', (string) $container->getAlias('lopi_pusher.authenticator'));
    }
}
