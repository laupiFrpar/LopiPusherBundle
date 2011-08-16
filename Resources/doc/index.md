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
    git=git://github.com/LaupiFrpar/LopiPusherBundle.git
    target=bundles/Lopi/Bundle/LopiPusherBundle
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
        new Lopi\Bundle\PusherBundle\LopiPuhserBundle(),
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
```

All parameters must correspond to http://app.pusherapp.com/apps/xxxx/api_access in the first block.

## Use Pusher

Now that you have completed the basic installation and configuration of the
LopiPusherBundle, you are ready to use the pusher.

``` php
<?php
$pusher = $this->container->get('lopi_pusher.pusher');
$pusher->trigger('channel name', 'event name', 'message');
```

If you want use the sockect id, 

``` php
<?php
<?php
$pusher = $this->container->get('lopi_pusher.pusher');
$pusher->trigger('channel name', 'event name', 'message', 'socket id');
```