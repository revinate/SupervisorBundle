# SupervisorBundle [![Build Status](https://secure.travis-ci.org/yzalis/SupervisorBundle.png)](http://travis-ci.org/yzalis/SupervisorBundle)

## About

This is the official bundle of the [Supervisor PHP library](https://github.com/yzalis/Supervisor).

## Installation

### Step 1: Install YZSupervisorBundle using [Composer](http://getcomposer.org)

Add YZSupervisorBundle in your `composer.json`:

    {
        "require": {
            "yzalis/supervisor-bundle": "v1.0@dev"
        }
    }

Now tell composer to download the bundle by running the command:

    $ php composer.phar update yzalis/supervisor-bundle

### Step 2: Enable the bundle

Enable the bundle in the kernel:

    <?php

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new YZ\Bundle\SupervisorBundle\SensioLabsConnectBundle(),
            // ...
        );
    }

### Step 3: Configure your `config.yml` file

```
# app/config/config.yml
yz_supervisor:
    default_environment: dev
    servers:
        prod:
            SUPERVISOR_01:
                host: 192.168.0.1
                username: guest
                password: password
                port: 9001
            SUPERVISOR_02:
                host: 192.168.0.2
                username: guest
                password: password
                port: 9001
        dev:
            locahost:
                host: 127.0.0.1
                username: guest
                password: password
                port: 9001
```

# Unit Tests

To run unit tests, you'll need cURL and a set of dependencies you can install using Composer:
```
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

Once installed, just launch the following command:
```
phpunit
```

You're done.

## Credits

* Benjamin Laugueux <benjamin@yzalis.com>
* [All contributors](https://github.com/yzalis/SupervisorBundle/contributors)

## License

Supervisor is released under the MIT License. See the bundled LICENSE file for details.
