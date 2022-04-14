<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\Tests\IntegrationTests;

use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorPresenceInterface;
use Lopi\Bundle\PusherBundle\Controller\AuthController;
use Lopi\Bundle\PusherBundle\Tests\LopiPusherTestKernel;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Pusher\Pusher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class PusherServiceDefinitionTest extends TestCase
{
    public function bundleServiceDefinitionDataProvider(): \Generator
    {
        $prefix = 'lopi_pusher.';

        yield [$prefix.'pusher',  Pusher::class, false];
        yield [AuthController::class, AuthController::class, true];
    }

    /**
     * @dataProvider bundleServiceDefinitionDataProvider
     */
    public function testBundleServiceDefinitions(string $serviceId, string $className): void
    {
        $container = $this->getConfiguredContainer($serviceId, ['auth_service_id' => ChannelAuthenticator::class]);
        $service = $container->get($serviceId);

        $this->assertInstanceOf($className, $service);
    }

    /**
     * @dataProvider bundleServiceDefinitionDataProvider
     */
    public function testBundleMinimalServiceDefinitions(string $serviceId, string $className, bool $expectException): void
    {
        if ($expectException) {
            $this->expectException(ServiceNotFoundException::class);
        }

        $container = $this->getConfiguredContainer($serviceId);
        $service = $container->get($serviceId);

        $this->assertInstanceOf($className, $service);
    }

    private function getConfiguredContainer(string $serviceId, array $bundleConfig = []): ContainerInterface
    {
        // Make private services public
        $pass = new DefinitionPublicCompilerPass();
        $pass->definition = $serviceId;

        $kernel = new PusherServiceDefinitionTestKernel(null, [], $bundleConfig);
        $kernel->compilerPass = $pass;
        $kernel->boot();

        return $kernel->getContainer();
    }
}

/**
 * @internal
 */
final class ChannelAuthenticator implements ChannelAuthenticatorPresenceInterface
{
    public function authenticate(string $socketId, string $channelName): bool
    {
    }

    public function getUserInfo(): array
    {
    }

    public function getUserId(): string
    {
    }
}

/**
 * @internal
 */
final class DefinitionPublicCompilerPass implements CompilerPassInterface
{
    public $definition;

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition($this->definition)) {
            $container->getDefinition($this->definition)
                ->setPublic(true);
        }
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
