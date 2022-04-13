<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Pierre-Louis Launay <laupi.frpar@gmail.com>
 */
class LopiPusherBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
