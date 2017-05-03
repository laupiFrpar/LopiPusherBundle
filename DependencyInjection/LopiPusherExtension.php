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

        if (!empty($config['url'])) {
            $config['app_id'] = substr(parse_url($config['url'], PHP_URL_PATH), 6);
            $config['key'] = parse_url($config['url'], PHP_URL_USER);
            $config['secret'] = parse_url($config['url'], PHP_URL_PASS);
            $config['scheme'] = parse_url($config['url'], PHP_URL_SCHEME);
            $config['host'] = parse_url($config['url'], PHP_URL_HOST);
            $config['port'] = parse_url($config['url'], PHP_URL_PORT) ?? $config['port'];
        }

        // For backwards compatibility with deprecated host argument
        if (preg_match('(^(https?://))', $config['host'], $matches)) {
            $config['scheme'] = substr($matches[0], 0, -3);
            $config['host'] = substr($config['host'], strlen($matches[0]));
        }

        $options = [
            'host' => $config['host'],
            'port' => $config['port'],
            'timeout' => $config['timeout'],
            'cluster' => $config['cluster'],
            'debug' => $config['debug'],
        ];

        $container->setParameter('lopi_pusher.app.id', $config['app_id']);
        $container->setParameter('lopi_pusher.key', $config['key']);
        $container->setParameter('lopi_pusher.secret', $config['secret']);
        $container->setParameter('lopi_pusher.options', $options);

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
