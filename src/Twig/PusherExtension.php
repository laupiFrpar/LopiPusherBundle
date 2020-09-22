<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle\Twig;

use Lopi\Bundle\PusherBundle\PusherConfiguration;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @author Pierre-Louis Launay <laupi.frpar@gmail.com>
 */
class PusherExtension extends AbstractExtension implements GlobalsInterface
{
    private $configuration;

    public function __construct(PusherConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getGlobals(): array
    {
        return [
            'pusher_key' => $this->configuration->getAuthKey()
        ];
    }
}
