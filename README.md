# Connect Package

This package provide Connect Client integration for Objective PHP applications.

With the Connect Package, your Objective PHP application integrate the SAML standard protocol and can act as a
Service Provider.

## Installation

Connect Package needs **PHP 7.0** or up, with the extension `mcrypt` plugged to run correctly.

You will have to integrate it to your Objective PHP project with `composer require fei/connect-package`

## Integration

In order to work properly, Connect-Client should be executed before application routing.

But in first place, and as shown below, the Connect Package must be plugged in the application initialization method.
The Connect Package create a Connect Client service that will be consume by the application's middlewares.

```php
<?php

use ObjectivePHP\Application\AbstractApplication;
use ObjectivePHP\Package\Connect\ConnectPackage;

class Application extends AbstractApplication
{
    public function init()
    {
        // Define some application steps
        $this->addSteps('bootstrap', 'init', 'auth', 'route', 'rendering');
        
        // Initializations...

        // Plugging the Connect Package in the bootstrap step
        $this->getStep('bootstrap')
            ->plug(ConnectPackage::class);

        // Another initializations...
    }
}
```

The `ConnectPackage` will also generate a ConnectResponse so it can handle SAML request and response protocol with the configured Identification Provider (IdP).

You can choose on which step you want to return this response. By default, the `ConnectPackage` will try to generate it in a Middleware in the `auth` step. You can change this step name in the constructor of the ConnectPackage like this :
 
 ```php
<?php
use ObjectivePHP\Package\Connect\ConnectPackage;

        $this->getStep('bootstrap')
            ->plug(new ConnectPackage('my_auth_step'));
```

By default if you don't specify your `auth` step and you don't have this step in your application workflow, `ConnectPackage` will generate this response in the same middleware of the `ConnectPackage`.

### Application configuration

Create a file in your configuration directory and put your SAML Metadata configuration as below:

```php
<?php
use ObjectivePHP\Package\Connect\Config\ConnectConfig;

return [
        (new ConnectConfig())
            ->setEntityID('http://project-url')
            // ... call your other setters
];
```

You can configure multiple things with the `ConnectConfig` class and its setters. Here are what you can configure:

| Setter                          | Description                                    	 | Default   |
|---------------------------------|--------------------------------------------------|-----------|
| `setDefaultTargetPath`          | Target path where the user is redirected | / |
| `setLogoutTargetPath`           | Target path where the user is redirected after logging out |     /      |
| `setEntityID`                   | Identifier (url) of the project that use this package| none|
| `setName`                       | Name (label) of the application| url of the application |
| `setIdpEntityID`                | Identifier (url) of the IDP | none |
| `setSamlMetadataBaseDir`        | Directory where the Saml metadata are stored| app/metadata|
| `setSpMetadataFile`             | Metadata file of the service provider| sp.xml |
| `setIdpMetadataFile`            | Metadata file of the identity provider| idp.xml |
| `setIdpMetadataFileTarget`      | Metadata file target of the identity provider|idp.xml|
| `setPrivateKeyFilePath`         | Path where the private key file is stored| app/key|
| `setAdminPathInfo`              | Endpoint where the administration of the client is made |  /connect/admin |

## Installation Command

For convenience, we provide a installation command. This command register the current application into your instance of
Connect-IDP, create the Service Provider metadata file (the famous sp.xml file) and the private key if its not exists.

Installation :

Put this directive into your config file :

```php
<?php

use ObjectivePHP\Cli\Config\CliCommand;
use ObjectivePHP\Package\Connect\Command\InstallCommand;

return [
    // ...
    new CliCommand(new InstallCommand())
];
```

Usage :

`vendor/bin/op usage connect:install`

Optional options :

* `--logout`: Logout url for the application (http://your.entityID/logout by default)
* `--acs`: Acs url for the application (http://your.entityID/acs by default)

The output of the install comment will print the sp.xml file content. This is useful for finishing the installation. 
