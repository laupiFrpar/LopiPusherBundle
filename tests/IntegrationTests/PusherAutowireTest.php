<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\Tests\IntegrationTests;

use Lopi\Bundle\PusherBundle\Tests\LopiPusherTestKernel;
use PHPUnit\Framework\TestCase;
use Pusher\Pusher;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PusherAutowireTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testPusherIsAutowiredByContainer(): void
    {
        $builder = new ContainerBuilder();
        $builder->autowire(PusherAutowireClass::class)
            ->setPublic(true)
        ;

        $kernel = new LopiPusherTestKernel($builder);
        $kernel->boot();

        $container = $kernel->getContainer();
        $container->get(PusherAutowireClass::class);

        if (method_exists($this, 'expectNotToPerformAssertions')) {
            $this->expectNotToPerformAssertions();
        }
    }
}

/**
 * @internal
 */
final class PusherAutowireClass
{
    public function __construct(Pusher $pusher)
    {
    }
}
