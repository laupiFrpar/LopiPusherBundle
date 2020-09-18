<?php

namespace Lopi\Bundle\PusherBundle\Tests;

use Lopi\Bundle\PusherBundle\LopiPusherBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class LopiPusherTestKernel extends Kernel
{
    private $builder;
    private $extraBundles;
    private $config;

    public function __construct(ContainerBuilder $builder = null, array $bundles = [], array $config = [])
    {
        $this->builder = $builder;
        $this->extraBundles = $bundles;
        $this->config = $config;

        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        return array_merge(
            [
                new FrameworkBundle(),
                new LopiPusherBundle()
            ],
            $this->extraBundles
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if (null === $this->builder) {
            $this->builder = new ContainerBuilder();
        }

        $builder = $this->builder;

        $loader->load(function (ContainerBuilder $container) use ($builder) {
            $container->merge($builder);
            $container->loadFromExtension(
                'framework',
                [
                    'secret' => 'foo',
                ]
            );

            $container->loadFromExtension(
                'lopi_pusher',
                array_merge(
                    [
                        'app_id' => 'fake_id',
                        'key' => 'fake_key',
                        'secret' => 'fake_secret'
                    ],
                    $this->config
                )

            );

            if ($container->hasExtension('twig')) {
                $container->loadFromExtension(
                    'twig',
                    [
                        'exception_controller' => null,
                        'strict_variables' => null,
                        'default_path' => __DIR__.'/Fixtures/templates'
                    ]
                );
            }

            $container->register('kernel', static::class)
                ->setPublic(true)
            ;
        });
    }

    public function getCacheDir()
    {
        return \sys_get_temp_dir().'/cache'.\spl_object_hash($this);
    }

    public function getLogDir()
    {
        return \sys_get_temp_dir().'/logs'.\spl_object_hash($this);
    }
}
