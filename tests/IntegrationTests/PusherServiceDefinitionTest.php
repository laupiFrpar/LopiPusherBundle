<?php

namespace Lopi\Bundle\PusherBundle\Tests\IntegrationTests;

use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorPresenceInterface;
use Lopi\Bundle\PusherBundle\Tests\LopiPusherTestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PusherServiceDefinitionTest extends TestCase
{
    public function bundleServiceDefinitionDataProvider(): \Generator
    {
        $prefix = 'lopi_pusher.';

        yield [$prefix.'pusher'];
        yield [$prefix.'auth_controller'];
    }

    /**
     * @dataProvider bundleServiceDefinitionDataProvider
     * @doesNotPerformAssertions
     */
    public function testBundleServiceDefinitions(string $definition): void
    {
        // Make private reset-password-bundle services public
        $pass = new DefinitionPublicCompilerPass();
        $pass->definition = $definition;

        $kernel = new PusherServiceDefinitionTestKernel(null, [], ['auth_service_id' => ChannelAuthenticator::class]);
        $kernel->compilerPass = $pass;
        $kernel->boot();

        $container = $kernel->getContainer();
        $container->get($definition);

        // If a service is not correctly defined, i.e. wrong class namespace, an exception will be thrown.
        if (method_exists($this, 'expectNotToPerformAssertions')) {
            $this->expectNotToPerformAssertions();
        }
    }
}

/**
 * @internal
 */
final class ChannelAuthenticator implements ChannelAuthenticatorPresenceInterface
{

    public function authenticate($socketId, $channelName)
    {
    }

    public function getUserInfo()
    {
    }

    public function getUserId()
    {
    }
}

/**
 * @internal
 */
final class DefinitionPublicCompilerPass implements CompilerPassInterface
{
    public $definition;

    public function process(ContainerBuilder $container)
    {
        $container->getDefinition($this->definition)
            ->setPublic(true)
        ;
    }
}

/**
 * @internal
 */
final class PusherServiceDefinitionTestKernel extends LopiPusherTestKernel
{
    public $compilerPass;

    protected function build(ContainerBuilder $container)
    {
        $container->addCompilerPass($this->compilerPass);
        $container->register(ChannelAuthenticator::class);
    }
}
