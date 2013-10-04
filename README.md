# LopiPusherBundle

[![Latest Stable Version](https://poser.pugx.org/laupifrpar/pusher-bundle/v/stable.png)](https://packagist.org/packages/laupifrpar/pusher-bundle)
[![Latest Unstable Version](https://poser.pugx.org/laupifrpar/pusher-bundle/v/unstable.png)](https://packagist.org/packages/laupifrpar/pusher-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/fc5c7590-2d84-47b0-b1e9-82b72c69767a/mini.png)](https://insight.sensiolabs.com/projects/fc5c7590-2d84-47b0-b1e9-82b72c69767a)
[![Build Status](https://secure.travis-ci.org/laupiFrpar/LopiPusherBundle.png)](http://travis-ci.org/laupiFrpar/LopiPusherBundle)

This bundle let you use Pusher simply.

[Pusher](http://pusher.com/) ([Documentation](http://pusher.com/docs)) is a simple hosted API for adding realtime bi-directional functionality via WebSockets to web and mobile apps, or any other Internet connected device.

This bundle is under the MIT license.

## Installation

Use the [composer](http://getcomposer.org) to install this bundle.

    $ composer require laupifrpar/pusher-bundle

Then update your `AppKernel.php` file, and register the new bundle:

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Lopi\Bundle\PusherBundle\LopiPusherBundle(),
        // ...
    );

## Configuration

If you have not a Pusher account, thank you to [sign up](https://app.pusherapp.com/accounts/sign_up) and make a note of your API key before continuing

### General

This is the default configuration in yml:

    # app/config/config.yml
    lopi_pusher:
        app_id: <your_app_id>
        key: <your_key>
        secret: <your_secret>

        # Default configuration
        debug: false # true if you want use the debug of all requests
        host: http://api.pusherapp.com
        port: 80
        timeout: 30

        # Optional configuration
        auth_service_id: <the_auth_service_id> # optional if you want to use private or presence channels

By default, calls will be made over a non-encrypted connection. To change this to make calls over HTTPS:

    # app/config/config.yml
    lopi_pusher:
        host: https://api.pusherapp.com
        port: 443

### Private and Presence channel authentication (optional)

If you'd like to use private or presence, you need to add an authorization service.

First, create an authorization service that implements `Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface`

    <?php
    // My/Bundle/AcmeBundle/Pusher/ChannelAuthenticator

    namespace My\Bundle\AcmeBundle\Pusher

    use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface

    class ChannelAuthenticator implements ChannelAuthenticationInterface
    {
        public function authenticate($socketId, $channelName)
        {
            // logic here
            …
            return true;
        }
    }

Then include it's **service id** in the lopi_pusher `auth_service_id` configuration parameter.

Additionally, enable the route by adding the following to your `app\config\routing.yml` configuration:

    # app\config\routing.yml
    lopi_pusher:
        resource: "@LopiPusherBundle/Resources/config/routing.xml"
        prefix:   /pusher

In some symfony configurations, you may need to manually specify the channel_auth_endpoint: (not required in most setups)

    <script type="text/javascript">
        Pusher.channel_auth_endpoint = "{{ path('lopi_pusher_bundle_auth') }}";
    </script>


## Use LopiPusherBundle

Get the pusher service

    <?php
    $pusher = $this->container->get('lopi_pusher.pusher');

See the [pusher's documentation](https://github.com/pusher/pusher-php-server#publishingtriggering-events) to use the pusher service

## Reporting an issue or a feature request

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/laupiFrpar/LopiPusherBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
