# Connect Package

This package provide Connect Client integration for Objective PHP v2 applications.

With the Connect Package, your Objective PHP application integrate the SAML standard protocol and can act as a
Service Provider.

## Installation

Connect Package needs **PHP 7.0** or up, with the extension `openssl` plugged to run correctly.

You will have to integrate it to your Objective PHP project with `composer require fei/connect-package`

## Integration

In order to work properly, Connect-Package must be register in your Application.

```php
<?php

namespace Fei\Service\Project;

use ObjectivePHP\Application\AbstractHttpApplication;
use Fei\Service\Connect\Package\ConnectPackage;

/**
 * Class Application
 *
 * @package Showcase
 */
class Application extends AbstractHttpApplication
{
    public function init()
    {        
        $this->registerPackage(new ConnectPackage());
        
        // Register other stuff
    }
}
```

By registering Connect-Package, your application will gain:

* Connect-Client, Connect-Config and Connect-User services with their associated injectors
* If your application is a HTTP Application (extending `ObjectivePHP\Application\AbstractHttpApplication`), Connect-Package middlewares

### Connect Services

Connect-Package exposes 3 services (see configuration section for service name purpose):

* `connect.config`: this is the configuration instance injected into Connect-Client constructor
* `connect.client`: the Connect-Client instance used to get Connect-User instance
* `connect.user` : the Connect-User instance representing the current identity of your application user

### Injectors

Injectors is an great feature of Objective-PHP services factory. With injectors, your services will be injected in the
right dependency by the services factory. Here below an example:

```php
<?php

namespace Fei\Service\Project\Services;

use Fei\Service\Connect\Package\UserAwareInterface;
use Fei\Service\Connect\Package\UserAwareTrait;

/**
 * Class Application
 *
 * @package Showcase
 */
class MyService implements UserAwareInterface
{
    use UserAwareTrait;
    
    public function myBusinessMethod()
    {
        // Doing my business  
        
        $this->getUser(); // Returns Connect-User service instance
        
        // Doing other business stuff
    }
}
```

By implementing Fei\Service\Connect\Package\UserAwareInterface, services factory will inject into your service the
current instance of Connect-User service thanks to injectors.

Connect-Package register 2 injectors:

* `Fei\Service\Connect\Package\Injector\UserAwareInjector`: will inject Connect-User service into your services that implement `Fei\Service\Connect\Package\UserAwareInterface` interface. 
* `Fei\Service\Connect\Package\Injector\ConnectAwareInjector`: will inject Connect-Client service into your services that implement `Fei\Service\Connect\Package\ConnectAwareInterface` interface.

Mostly, you will prefer to inject Connect-User service. In this way, you will decrease the coupling of your service
layer with Connect-Client.

### Disable Connect behaviours and Mocked User

You can disable the Connect middlewares and so disable Connect behaviour. It's commonly used in development environment
(Connect-IDP is not installed for example).

If you disable Connect middleware, Connect-Package will replace Connect-User instance by a mocked Connect-User instance
you could configure.

In this way, your application will work as if it's wired to Connect-IDP.

### Configuration

Connect-Package look like below:

```json
{
    "connect": {
        "enable": true,
        "name": "SP name",
        "entity_id": "http://sp.com",
        "idp_entity_id": "http://idp.com",
        "client_service_id": "connect.client",
        "config_service_id": "connect.config",
        "user_service_id": "connect.user",
        "mock_user" : {
            "username": "Gauthier",
            "current_role": "SUPER_ADMIN"
        },
        "default_target_path": "/",
        "logout_target_path": "/",
        "profile_association_path": "/connect/profile",
        "profile_association_service_id": "connect.profile-association",
        "saml_metadata_basedir": "app/config/Saml/metadata",
        "sp_metadata_file": "sp.xml",
        "idp_metadata_file": "idp.xml",
        "idp_metadata_file_target": "/idp.xml",
        "private_key_file_path": "/some/path/key/key.pem",
        "admin_path_info": "/connect/admin",
        "allowed_roles": ["USER", "ADMIN"],
        "filters": ["service.filter.first", "service.filter.second"]
    }
}
```

| key                              | Description                                                | Default                        | Mandatory                    |
|----------------------------------|------------------------------------------------------------|--------------------------------|------------------------------|
| `enable`                         | Enable connect client behaviours                           | `true`                         | yes                          |
| `name`                           | Application name (set to entityId if null)                 | `null`                         | no                           |
| `entity_id`                      | Service Provider entity id (must be an URL)                | `null`                         | yes                          |
| `idp_entity_id`                  | Identity Provider entity id (must be an URL)               | `null`                         | yes                          |
| `client_service_id`              | Connect-Client service id                                  | `connect.client`               | yes                          |
| `config_service_id`              | Connect-Client service id                                  | `connect.config`               | yes                          |
| `user_service_id`                | Connect-Client service id                                  | `connect.user`                 | yes                          |
| `mock_user`                      | Connect-User service mocked when `enable` is `false`       | `null`                         | yes when `enable` is `false` |
| `default_target_path`            | Target path where the user is redirected after logging in  | `/`                            | yes                          |
| `logout_target_path`             | Target path where the user is redirected after logging out | `/`                            | yes                          |
| `profile_association_path`       | Profile association pathinfo endpoint                      | `/connect/profile-association` | yes                          |
| `profile_association_service_id` | Service id for profile association callback                | `null`                         | no                           |
| `saml_metadata_basedir`          | Path of the SAML metadata (relative to project root)       | `app/config/Saml/metadata`     | yes                          |
| `sp_metadata_file`               | SP metadata file name                                      | `sp.xml`                       | yes                          |
| `idp_metadata_file`              | SP metadata file name                                      | `idp.xml`                      | yes                          |
| `idp_metadata_file_target`       | IDP metadata pathinfo                                      | `/idp.xml`                     | yes                          |
| `private_key_file_path`          | SP private key file path (relative to project root)        | `app/config/key/sp.pem`        | yes                          |
| `admin_path_info`                | Administrative pathinfo endpoint                           | `/connect/admin`               | yes                          |
| `allowed_roles`                  | User current roles allowed                                 | `['USER']`                     | yes                          |
| `filters`                        | Services id for middleware filter                          | `[]`                           | yes                          |

### Final word on filters

Yes, yes, after this point your will be ready to use Connect-Package and enjoy with Objective-PHP.

So filters...

Filters is a way to activate or not Connect middlewares. There are the same effect that `enable` option but with the
execution context (like disable Connect for API call for example).

A filter is a class that implement `ObjectivePHP\Filter\FilterInterface` and when it's run by the package, it's decide
if the Connect middlewares must be register or not.

Before use filters you must register into services factory the your filters descriptions.
