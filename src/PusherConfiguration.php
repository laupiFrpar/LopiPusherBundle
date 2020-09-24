<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lopi\Bundle\PusherBundle;

final class PusherConfiguration
{
    private $authKey;
    private $secret;
    private $appId;
    private $options;

    public function __construct(array $config)
    {
        if (!empty($config['url'])) {
            $config['app_id'] = substr(parse_url($config['url'], PHP_URL_PATH), 6);
            $config['key'] = parse_url($config['url'], PHP_URL_USER);
            $config['secret'] = parse_url($config['url'], PHP_URL_PASS);
            $config['scheme'] = parse_url($config['url'], PHP_URL_SCHEME);
            $config['host'] = parse_url($config['url'], PHP_URL_HOST);
            $config['port'] = parse_url($config['url'], PHP_URL_PORT) ?: $config['port'];
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
        ];
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

    public function getOptions(): array
    {
        return $this->options;
    }
}
