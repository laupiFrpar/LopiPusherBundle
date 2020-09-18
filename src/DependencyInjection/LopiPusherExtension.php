<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\DependencyInjection;

use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface;
use Pusher\Pusher;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Twig\Extension\AbstractExtension;

/**
 * LopiPusherExtension
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

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (null === $config['auth_service_id']) {
            $container->removeDefinition('lopi_pusher.auth_controller');
        }

        if ($container->hasDefinition('lopi_pusher.auth_controller')) {
            $controllerDefinition = $container->getDefinition('lopi_pusher.auth_controller');
            $controllerDefinition->setArgument(0, $config);
            $controllerDefinition->setArgument(1, new Reference($config['auth_service_id']));
        }

        $pusherDefinition = $container->getDefinition('lopi_pusher.pusher');
        $pusherDefinition->setArgument(0, $config);

        $container->setAlias(Pusher::class, 'lopi_pusher.pusher')->setPublic(true);

        if (class_exists(AbstractExtension::class)) {
            $loader->load('twig.xml');
        }
    }
}
