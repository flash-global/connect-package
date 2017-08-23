<?php

namespace ObjectivePHP\Package\Connect;

use Fei\ApiClient\Transport\BasicTransport;
use Fei\Service\Connect\Client\Config;
use Fei\Service\Connect\Client\Connect;
use Fei\Service\Connect\Client\Metadata;
use Fei\Service\Connect\Client\Saml;
use Fei\Service\Connect\Client\Token;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Application\Config\Param;
use ObjectivePHP\Package\Connect\Config\ConnectConfig;
use ObjectivePHP\Package\Connect\Config\IdentityProviderParam;
use ObjectivePHP\Package\Connect\Config\PrivateKey;
use ObjectivePHP\Package\Connect\Config\ServiceProvider;
use ObjectivePHP\Package\Connect\Middleware\ConnectMiddleware;
use ObjectivePHP\ServicesFactory\ServiceReference;
use ObjectivePHP\ServicesFactory\ServicesFactory;

/**
 * Class ConnectPackage
 *
 * @package ObjectivePHP\Package\Connect
 */
class ConnectPackage
{
    const API_SAFE_MODE = 1;

    /** @var  string */
    protected $authenticationStep;

    /** @var  string */
    protected $apiMatcher;

    /** @var  int */
    protected $flags;

    /**
     * ConnectPackage constructor.
     * @param string $authenticationStep
     * @param string $apiMatcher
     * @param int $flags
     */
    public function __construct(string $authenticationStep = 'auth', string $apiMatcher = '/api/*', int $flags = 0)
    {
        $this->authenticationStep = $authenticationStep;
        $this->apiMatcher = $apiMatcher;
        $this->flags = $flags;
    }

    /**
     * Invoke magic method
     *
     * @param ApplicationInterface $app
     * @throws \Exception
     */
    public function __invoke(ApplicationInterface $app)
    {
        if (!$app->getSteps()->has($this->authenticationStep)) {
            throw new \Exception(sprintf('Step `%s` not found!', $this->authenticationStep));
        }

        $app->getStep($this->authenticationStep)->plugFirst(new ConnectMiddleware($this->apiMatcher, $this->flags));

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
            ],
            [
                'id' =>'connect.client.token',
                'class' => Token::class,
                'params' => [[Token::OPTION_BASEURL => $app->getConfig()->subset(Param::class)->get('connect.url')]],
                'setters' => [
                    'setTransport' => [new BasicTransport()]
                ]
            ]
        );
    }
}
