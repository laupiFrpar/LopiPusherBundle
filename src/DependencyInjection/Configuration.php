<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('lopi_pusher');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->validate()
                ->ifTrue(
                    function ($data) {
                        return empty($data['url'])
                            && (
                                empty($data['app_id'])
                                || empty($data['key'])
                                || empty($data['secret'])
                            );
                    }
                )
                ->thenInvalid('Either url or app_id, key and secret needs to be set.')
            ->end()
            ->children()
                ->scalarNode('url')->end()
                ->scalarNode('app_id')->end()
                ->scalarNode('key')->end()
                ->scalarNode('secret')->end()
                ->scalarNode('cluster')->defaultValue('us-east-1')->end()
                ->booleanNode('debug')->defaultValue(false)->end()
                ->booleanNode('verifySSL')->defaultValue(true)->end()
                ->scalarNode('scheme')->defaultValue('http')->end()
                ->scalarNode('host')->defaultValue('api.pusherapp.com')->end()
                ->scalarNode('port')->defaultValue('80')->end()
                ->scalarNode('timeout')->defaultValue('30')->end()
                ->scalarNode('auth_service_id')->defaultNull()->end()
                ->scalarNode('encryption_master_key_base64')->defaultNull()->end()
            ->end();

        return $treeBuilder;
    }
}
