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

Finally we could plug the Connect Middleware. The purpose of the connect middleware is to handle SAML request and
response protocol with the configured Identification Provider (IdP).

```php
<?php

use ObjectivePHP\Application\AbstractApplication;
use ObjectivePHP\Package\Connect\ConnectPackage;
use ObjectivePHP\Package\Connect\Middleware\ConnectMiddleware;
use ObjectivePHP\Application\ApplicationInterface;

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

        // Another initialization...

        $this->getStep('auth')
            // Here we plug the Connect Middleware
            ->plug(ConnectMiddleware::class)
            ->plug(function (ApplicationInterface $app) {
                // Check if current authenticate user has the right role
                if ($app->getServicesFactory()->get('connect.client')->getUser()->getCurrentRole() != 'ADMIN') {
                    throw new Exception('Unauthorized', 401);
                }
            });

         // Another initialization...
    }
}
```

### Application configuration

Create a file in your configuration directory and put your SAML Metadata configuration as below:

```php
<?php

use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\SingleLogoutService;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\SamlConstants;
use ObjectivePHP\Package\Connect\Config\IdentityProviderParam;
use ObjectivePHP\Package\Connect\Config\PrivateKey;
use ObjectivePHP\Package\Connect\Config\ServiceProvider;

return [
    // The Service Provider configuration
    new ServiceProvider(
        (new SpSsoDescriptor())
            // The ID of the service provider: must be the base URL of your project
            ->setID('http://my.sp.com')
            // Configure the Assertion Consumer Service endpoint
            ->addAssertionConsumerService(
                new AssertionConsumerService(
                    'http://my.sp.com/acs',
                    SamlConstants::BINDING_SAML2_HTTP_POST
                )
            )
            // Configure the logout endpoint
            ->addSingleLogoutService(
                new SingleLogoutService(
                    'http://my.sp.com/logout',
                    SamlConstants::BINDING_SAML2_HTTP_POST
                )
            )
            // Add a certificate for signing the Service Provider message
            ->addKeyDescriptor(new KeyDescriptor(
                KeyDescriptor::USE_SIGNING,
                X509Certificate::fromFile(__DIR__ . '/keys/sp.crt')
            ))
            // Add a certificate for encrypting the Service Provider message
            ->addKeyDescriptor(new KeyDescriptor(
                KeyDescriptor::USE_ENCRYPTION,
                X509Certificate::fromFile(__DIR__ . '/keys/sp.crt')
            ))
    ),
    new IdentityProviderParam(
        'default',
        (new IdpSsoDescriptor())
            // The ID of your Identity Provider (IdP): must be the base URL of the IdP 
            ->setID('http://your.idp.com')
            // Tell if your Service Provider must provide signed AuthnRequest to the IdP 
            ->setWantAuthnRequestsSigned(true)
            // Configure the IdP SSO endpoint where your Service Provider must send AuthnRequest
            ->addSingleSignOnService(
                new SingleSignOnService('http://your.idp.com/sso', SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
            )
            // Configure the IdP logout endpoint where your Service Provider must initiate the logout behaviour 
            ->addSingleLogoutService(
                new SingleLogoutService('http://your.idp.com/logout', SamlConstants::BINDING_SAML2_HTTP_POST)
            )
            // The IdP public certificate
            ->addKeyDescriptor(new KeyDescriptor(
                KeyDescriptor::USE_SIGNING,
                X509Certificate::fromFile(__DIR__ . '/keys/idp/idp.crt')
            ))
    ),
    // Configure the Service Provider private key
    new PrivateKey(file_get_contents(__DIR__ . '/keys/sp.pem'))
];
```

Please check out `connect-client` documentation for more information about SAML Metadata configuration.
