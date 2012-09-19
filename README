# LopiPusherBundle

[![Build Status](https://secure.travis-ci.org/laupiFrpar/LopiPusherBundle.png)](http://travis-ci.org/laupiFrpar/LopiPusherBundle)

This bundle let you use Pusher simply.

[Pusher](http://pusher.com/) ([Documentation](http://pusher.com/docs)) is a simple hosted API for adding realtime bi-directional functionality via WebSockets to web and mobile apps, or any other Internet connected device.

This bundle is under the MIT license.

## Installation

To install LopiPubsherBundle with Composer just add the following to your `composer.json` file:

    // composer.json
    {
        // ...
        require: {
            // ...
            "laupifrpar/pusher-bundle": "dev-master"
        }
    }

Then, you can install the new dependencies by running Composer's `update`
command from the directory where your `composer.json` file is located:

    $ php composer.phar update

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your `AppKernel.php` file, and
register the new bundle:

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

This is the configuration in yml

    # app/config/config.yml
    lopi_pusher:
        app_id: xxx
	    key: xxx
	    secret: xxx
	    auth_service_id: xxx # optional if you want to use private or presence channels

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
        resource: "@LopiPusherBundle/Resources/config/routing.yml"
        prefix:   /pusher/auth

In some symfony configurations, you may need to manually specify the channel_auth_endpoint: (not required in most setups)

    <script type="text/javascript">
        Pusher.channel_auth_endpoint = "{{ path('lopi_pusher_bundle_auth') }}";
    </script>


## Use LopiPusherBundle

Now that you have completed the basic installation and configuration of the
LopiPusherBundle, you are ready to use the pusher.

    <?php
    $pusher = $this->container->get('lopi_pusher.pusher');
    $pusher->trigger('channel name', 'event name', 'message');

If you want use the socket id,

    <?php
    $pusher = $this->container->get('lopi_pusher.pusher');
    $pusher->trigger('channel name', 'event name', 'message', 'socket id');

## Reporting an issue or a feature request

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/laupiFrpar/LopiPusherBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.