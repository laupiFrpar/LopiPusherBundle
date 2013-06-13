<?php

namespace Lopi\Bundle\PusherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * PusherBundle configuration structure.
 *
 * @author Pierre-Louis LAUNAY <laupi.frpar@gmail.com>
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lopi_pusher', 'array');

        $rootNode
            ->children()
                ->scalarNode('app_id')->isRequired()->end()
                ->scalarNode('key')->isRequired()->end()
                ->scalarNode('secret')->isRequired()->end()
                ->booleanNode('debug')->defaultValue(false)->end()
                ->scalarNode('host')->defaultValue('http://api.pusherapp.com')->end()
                ->scalarNode('port')->defaultValue('80')->end()
                ->scalarNode('timeout')->defaultValue('30')->end()
                ->scalarNode('auth_service_id')->defaultNull()->end()
            ->end();

        return $treeBuilder;
    }
}
