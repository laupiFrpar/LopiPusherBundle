Getting Started With LopiPusherBundle
=====================================

Pusher is a simple hosted API for adding realtime bi-directional functionality via WebSockets to web and mobile apps, or any other Internet connected device.

The documentation is here : http://pusher.com/docs
The website is : http://pusher.com/

This bundle let you use Pusher simply.

## Installation

Installation is a quick (I promise!) 4 step process:

1. Download LopiPusherBundle
2. Configure the Autoloader
3. Enable the Bundle
4. Configure your application

### Step 1: Download LopiPusherBundle

Ultimately, the LopiPusherBundle files should be downloaded to the
`vendor/bundles/Lopi/Bundle/PusherBundle` directory.

This can be done in several ways, depending on your preference. The first
method is the standard Symfony2 method.

**Using the vendors script**

Add the following lines in your `deps` file:

```
[LopiPusherBundle]
    git=http://github.com/laupiFrpar/LopiPusherBundle.git
    target=bundles/Lopi/Bundle/PusherBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

**Using submodules**

If you prefer instead to use git submodules, the run the following:

``` bash
$ git submodule add git://github.com/LaupiFrpar/LopiPusherBundle.git vendor/bundles/Lopi/Bundle/PusherBundle
$ git submodule update --init
```

### Step 2: Configure the Autoloader

Add the `Lopi` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Lopi' => __DIR__.'/../vendor/bundles',
));
```

### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Lopi\Bundle\PusherBundle\LopiPusherBundle(),
    );
}
```

### Step 4: Configure your application

You must configure the pusher

``` yaml
# app/config/config.yml
lopi_pusher:
    app_id: xxx
	key: xxx
	secret: xxx
	auth_service_id: xxx # optional if you want to use private or presence channels
```

All parameters must correspond to http://app.pusherapp.com/apps/xxxx/api_access in the first block.

### Step 4: Private and Presence channel authentication (optional)

If you'd like to use private or presence, you need to add an authorization service.  First, create an authorization
service that implements `Lopi\Bundle\PusherBundle\Authenticator\ChannelAuthenticatorInterface` and include it's
service id in the lopi_pusher `auth_service_id` configuration parameter.

``` php
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
```

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

``` php
<?php
$pusher = $this->container->get('lopi_pusher.pusher');
$pusher->trigger('channel name', 'event name', 'message');
```

If you want use the socket id,

``` php
<?php
<?php
$pusher = $this->container->get('lopi_pusher.pusher');
$pusher->trigger('channel name', 'event name', 'message', 'socket id');
```