<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IntegrationTests;

use Lopi\Bundle\PusherBundle\Tests\LopiPusherTestKernel;
use Lopi\Bundle\PusherBundle\Twig\PusherExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Twig\Environment;

final class PusherTwigExtensionTest extends TestCase
{
    public function testExtensionIsLoaded()
    {
        $kernel = new LopiPusherTestKernel(null, [new TwigBundle()], ['key' => 'test_key']);
        $kernel->boot();

        $container = $kernel->getContainer();
        /** @var Environment $twig */
        $twig = $container->get('twig');

        $this->assertInstanceOf(PusherExtension::class, $twig->getExtension(PusherExtension::class));
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('test_key', $twig->render('sample.html.twig'));
        } else {
            $this->assertContains('test_key', $twig->render('sample.html.twig'));
        }
    }
}
