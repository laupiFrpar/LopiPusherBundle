<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle;

/**
 * @author Pierre-Louis Launay
 */
final class PusherConfiguration
{
    private string $authKey;
    private string $secret;
    private string $appId;
    private array $options;

    public function __construct(array $config)
    {
        if (!empty($config['url'])) {
            $config['app_id'] = substr(parse_url($config['url'], \PHP_URL_PATH), 6);
            $config['key'] = parse_url($config['url'], \PHP_URL_USER);
            $config['secret'] = parse_url($config['url'], \PHP_URL_PASS);
            $config['scheme'] = parse_url($config['url'], \PHP_URL_SCHEME);
            $config['host'] = parse_url($config['url'], \PHP_URL_HOST);
            $config['port'] = parse_url($config['url'], \PHP_URL_PORT) ?: $config['port'];
        }

        // For backwards compatibility with deprecated host argument
        if (preg_match('(^(https?://))', $config['host'], $matches)) {
            $config['scheme'] = substr($matches[0], 0, -3);
            $config['host'] = substr($config['host'], \strlen($matches[0]));
        }

        $this->authKey = $config['key'];
        $this->secret = $config['secret'];
        $this->appId = $config['app_id'];

        $this->options = [
            'scheme' => $config['scheme'],
            'host' => $config['host'],
            'port' => $config['port'],
            'timeout' => $config['timeout'],
            'cluster' => $config['cluster'],
            'debug' => $config['debug'],
            // Configuration for Guzzle:
            'clientConfig' => [],
        ];

        if (is_bool($config['verifySSL'] ?? null)) {
            $this->options['clientConfig']['verify'] = $config['verifySSL'];
        }

        if ($encryptionKey = $config['encryption_master_key_base64'] ?? false) {
            $this->options['encryption_master_key_base64'] = $encryptionKey;
        }
    }

    public function getAuthKey(): string
    {
        return $this->authKey;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @phpstan-return array{scheme: string, host: string, port: positive-int, timeout: positive-int, cluster: string, debug: bool, clientConfig: array<mixed>}
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
