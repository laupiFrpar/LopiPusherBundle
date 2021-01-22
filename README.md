# LopiPusherBundle

[![Build Status](https://travis-ci.org/laupiFrpar/LopiPusherBundle.svg?branch=master)](https://travis-ci.org/laupiFrpar/LopiPusherBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/fc5c7590-2d84-47b0-b1e9-82b72c69767a/mini.png)](https://insight.sensiolabs.com/projects/fc5c7590-2d84-47b0-b1e9-82b72c69767a)
[![Maintainability](https://api.codeclimate.com/v1/badges/76235ea329f7ea57834b/maintainability)](https://codeclimate.com/github/laupiFrpar/LopiPusherBundle/maintainability)

This bundle let you use Pusher simply.

[Pusher](http://pusher.com/) ([Documentation](http://pusher.com/docs)) is a simple
hosted API for adding realtime bi-directional functionality via WebSockets to web
and mobile apps, or any other Internet connected device. It's super powerful, and
a ton of fun!

This bundle is under the MIT license.

## Installation

Use [composer](http://getcomposer.org) to install this bundle.

```bash
composer require laupifrpar/pusher-bundle
```

If you're not using Symfony Flex, then you will also need to enable 
`Lopi\Bundle\PusherBundle\LopiPusherBundle` in your `config/bundles.php` file.

```php
<?php

return [
    // ...
    Lopi\Bundle\PusherBundle\LopiPusherBundle::class => ['all' => true],
    // ...
];
```

## Configuration

If you do *not* have a Pusher account, [sign up](https://app.pusherapp.com/accounts/sign_up)
and make a note of your API key before continuing.

### General

To start, you'll need to setup a bit of configuration. 

This is the default configuration in yml:

```yml
# app/config/config.yml
lopi_pusher:
    # Default configuration
    scheme: http
    host: api.pusherapp.com
    port: 80
    cluster: us-east-1 # Change the cluster name
    timeout: 30
    debug: false # true if you want use the debug of all requests
```

You must set the `url` parameter :

```yml
# app/config/config.yml
lopi_pusher:
    url: <scheme>://<key>:<secret>@<host>[:<port>]/apps/<app-id>
```

It will parse the URL and set, or replace the default value if exists, the various parameters `scheme`, `key`, `secret`, `host`, `port` and `app_id`

Or you can set the various parameters separately:

```yml
# app/config/config.yml
lopi_pusher:
    app_id: <app-id>
    key: <key>
    secret: <secret>
```

By default, calls will be made over a non-encrypted connection. To change this to
make calls over HTTPS, simply:

```yml
# app/config/config.yml
lopi_pusher:
    # ...
    scheme: https
    port: 443
```

If you want to use private or presence channels, set the parameter `auth_service_id`

```yml
# app/config/config.yml
lopi_pusher:
    # ...
    auth_service_id: <the_auth_service_id>
```

See the section about "Private and Presense channel auth" below

## Usage!

Once you've configured the bundle, you will have access to a pusher service,
which can be autowired by `Pusher\Pusher` typehint.
From inside a controller, you can use it like this:

```php
use Pusher\Pusher;

class SampleController 
{
    public function triggerPusherAction(Pusher $pusher)
    {
        // ...
    
        $data['message'] = 'hello world';
        $pusher->trigger('test_channel', 'my_event', $data);
    
        // ...
    }
}
```

This code will autowire `\Pusher\Pusher` class from the official Pusher SDK. You can find out all about it on
[pusher's documentation](https://github.com/pusher/pusher-php-server#publishingtriggering-events).

## Private and Presence channel authentication (optional)

If you'd like to use private or presence, you need to add an authorization service.

First, create an authorization service that implements `Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface`:

```php
<?php
// src/Pusher/ChannelAuthenticator.php

namespace App\Pusher;

use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface;

class ChannelAuthenticator implements ChannelAuthenticatorInterface
{
    public function authenticate($socketId, $channelName)
    {
        // logic here

        return true;
    }
}
```

Next, register it as service like normal:

```yml
# config/services.yml
services:
    my_channel_authenticator: AppBundle\Pusher\ChannelAuthenticator
```

Then include its **service id** in the lopi_pusher `auth_service_id` configuration
parameter:

```yml
# config/packagers/lopi_pusher.yml
lopi_pusher:
    # ...

    auth_service_id: 'my_channel_authenticator'
```

Additionally, enable the route by adding the following to your `config\routing.yml`
configuration:

```yml
# config\routing.yml
lopi_pusher:
    resource: "@LopiPusherBundle/Resources/config/routing.xml"
    prefix:   /pusher
```

In some Symfony configurations, you may need to manually specify the
`channel_auth_endpoint`: (not required in most setups):

```twig
{# app/Resources/views/base.html.twig #}

<script type="text/javascript">
    Pusher.channel_auth_endpoint = "{{ path('lopi_pusher_bundle_auth') }}";
</script>
```

## Reporting an issue or a feature request

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/laupiFrpar/LopiPusherBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
