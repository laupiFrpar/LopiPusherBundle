# Getting Started With LopiPusherBundle

Pusher is a simple hosted API for adding realtime bi-directional functionality via WebSockets to web and mobile apps, or any other Internet connected device.

The documentation is here : http://pusher.com/docs
The website is : http://pusher.com/

This bundle let you use Pusher simply.

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

Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

    $ php composer.phar update

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Lopi\Bundle\PusherBundle\LopiPusherBundle(),
        // ...
    );

### Configure your application

You must configure the pusher

    # app/config/config.yml
    lopi_pusher:
        app_id: xxx
	    key: xxx
	    secret: xxx
	    auth_service_id: xxx # optional if you want to use private or presence channels

All parameters must correspond to http://app.pusherapp.com/apps/xxxx/api_access in the first block.

### Step 4: Private and Presence channel authentication (optional)

If you'd like to use private or presence, you need to add an authorization service.  First, create an authorization
service that implements `Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface` and include it's
service id in the lopi_pusher `auth_service_id` configuration parameter.

    <?php
    // My/Bundle/AcmeBundle/Pusher/ChannelAuthenticator

    namespace My\Bundle\AcmeBundle\Pusher

    use Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface

    class ChannelAuthenticator implements ChannelAuthenticationInterface
    {
        public function authenticate($socketId, $channelName)
        {
            // logic here

            return true;
        }
    }

Additionally, enable the route by adding the following to your `app\config\routing.yml` configuration:

    # app\config\routing.yml
    lopi_pusher:
        resource: "@LopiPusherBundle/Resources/config/routing.yml"
        prefix:   /pusher/auth

In some symfony configurations, you may need to manually specify the channel_auth_endpoint: (not required in most setups)

    <script type="text/javascript">
        Pusher.channel_auth_endpoint = "{{ path('lopi_pusher_bundle_auth') }}";
    </script>


## Use Pusher

Now that you have completed the basic installation and configuration of the
LopiPusherBundle, you are ready to use the pusher.

    <?php
    $pusher = $this->container->get('lopi_pusher.pusher');
    $pusher->trigger('channel name', 'event name', 'message');

If you want use the socket id,

    <?php
    $pusher = $this->container->get('lopi_pusher.pusher');
    $pusher->trigger('channel name', 'event name', 'message', 'socket id');