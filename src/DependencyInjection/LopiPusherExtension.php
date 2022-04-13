<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\DependencyInjection;

use Lopi\Bundle\PusherBundle\Controller\AuthController;
use Pusher\Pusher;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Twig\Extension\AbstractExtension;

/**
 * LopiPusherExtension.
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

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.xml');

        $pusherConfigurationDefinition = $container->getDefinition('lopi_pusher.pusher_configuration');
        $pusherConfigurationDefinition->setArgument(0, $config);

        if (null === $config['auth_service_id']) {
            $container->removeDefinition(AuthController::class);
        } else {
            $controllerDefinition = $container->getDefinition(AuthController::class);
            $controllerDefinition->setArgument(1, new Reference($config['auth_service_id']));
        }

        $container->setAlias(Pusher::class, 'lopi_pusher.pusher');

        if (class_exists(AbstractExtension::class)) {
            $loader->load('twig.xml');
        }
    }
}
