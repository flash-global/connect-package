<?php

namespace ObjectivePHP\Package\Connect;

use Fei\Service\Connect\Client\Config;
use Fei\Service\Connect\Client\Connect;
use Fei\Service\Connect\Client\Metadata;
use Fei\Service\Connect\Client\Saml;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Package\Connect\Config\ConnectConfig;
use ObjectivePHP\Package\Connect\Config\IdentityProviderParam;
use ObjectivePHP\Package\Connect\Config\PrivateKey;
use ObjectivePHP\Package\Connect\Config\ServiceProvider;
use ObjectivePHP\ServicesFactory\ServiceReference;

/**
 * Class ConnectPackage
 *
 * @package ObjectivePHP\Package\Connect
 */
class ConnectPackage
{
    /**
     * Invoke magic method
     *
     * @param ApplicationInterface $app
     */
    public function __invoke(ApplicationInterface $app)
    {
        if ($app->getConfig()->get(ConnectConfig::class)) {
            $directive = $app->getConfig()->get(ConnectConfig::class);

            $callback = $directive['profileAssociationCallback'];

            $app->getServicesFactory()->registerService([
                'id' => 'connect.config',
                'class' => Config::class,
                'setters' => [
                    'setDefaultTargetPath' => [$directive['defaultTargetPath']],
                    'setLogoutTargetPath' => [$directive['logoutTargetPath']],
                ]
            ]);

            if ($callback) {
                $app->getServicesFactory()->registerService([
                    'id' => 'connect.config',
                    'class' => Config::class,
                    'setters' => [
                        'setDefaultTargetPath' => [$directive['defaultTargetPath']],
                        'setLogoutTargetPath' => [$directive['logoutTargetPath']],
                        'registerProfileAssociation' => [$callback, $directive['profileAssociationPath']]
                    ]
                ]);
            }
        } else {
            $app->getServicesFactory()->registerService([
                'id' =>'connect.config',
                'class' => Config::class
            ]);
        }

        $app->getServicesFactory()->registerService(
            [
                'id' =>'connect.client',
                'class' => Connect::class,
                'params' => [new ServiceReference('connect.saml'), new ServiceReference('connect.config')]
            ],
            [
                'id' =>'connect.saml',
                'class' => Saml::class,
                'params' => [new ServiceReference('connect.metadata')]
            ],
            [
                'id' =>'connect.metadata',
                'class' => Metadata::class,
                'setters' => [
                    'setIdentityProvider' => [$app->getConfig()->subset(IdentityProviderParam::class)['default']],
                    'setServiceProvider' => [
                        $app->getConfig()->get(ServiceProvider::class),
                        $app->getConfig()->get(PrivateKey::class)
                    ]
                ]
            ]
        );
    }
}
