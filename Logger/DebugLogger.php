<?php

namespace Lopi\Bundle\PusherBundle\Logger;

use Psr\Log\LoggerInterface;

/**
 * DebugLogger
 *
 * @author Robin van der Vleuten <robin@seigyo.io>
 */
class DebugLogger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function log($message)
    {
        $this->logger->debug($message);
    }
}
