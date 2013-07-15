<?php

namespace Lopi\Bundle\PusherBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * LopiPusherExtension
 *
 */
class LopiPusherExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lopi_pusher.app.id', $config['app_id']);
        $container->setParameter('lopi_pusher.key', $config['key']);
        $container->setParameter('lopi_pusher.secret', $config['secret']);
        $container->setParameter('lopi_pusher.debug', $config['debug']);
        $container->setParameter('lopi_pusher.host', $config['host']);
        $container->setParameter('lopi_pusher.port', $config['port']);
        $container->setParameter('lopi_pusher.timeout', $config['timeout']);

        if (null !== $config['auth_service_id']) {
            $container->setAlias('lopi_pusher.authenticator', $config['auth_service_id']);
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'lopi_pusher';
    }
}
