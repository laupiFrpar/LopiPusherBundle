<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Twig\Extension\AbstractExtension;

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

        $container->setParameter('lopi_pusher.config', $config);

        if (null !== $config['auth_service_id']) {
            $container->setAlias('lopi_pusher.authenticator', $config['auth_service_id'])->setPublic(true);
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (class_exists(AbstractExtension::class)) {
            $loader->load('twig.xml');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'lopi_pusher';
    }
}
