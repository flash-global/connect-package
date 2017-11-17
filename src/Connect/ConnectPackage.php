<?php

namespace ObjectivePHP\Package\Connect;

use Fei\Service\Connect\Client\Config\Config;
use Fei\Service\Connect\Client\Connect;
use Fei\Service\Connect\Common\Entity\User;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Application\Session\Session;
use ObjectivePHP\Application\Workflow\Step;
use ObjectivePHP\Package\Connect\Config\ConnectConfig;
use ObjectivePHP\ServicesFactory\ServiceReference;

/**
 * Class ConnectPackage
 *
 * @package ObjectivePHP\Package\Connect
 */
class ConnectPackage
{
    /** @var string */
    protected $authStepName;

    /**
     * ConnectPackage constructor.
     *
     * @param string $authStepName
     */
    public function __construct(string $authStepName = 'auth')
    {
        $this->setAuthStepName($authStepName);
    }

    /**
     * Invoke magic method
     *
     * @param ApplicationInterface $app
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ApplicationInterface $app)
    {
        $app->getServicesFactory()->registerService(...$this->getServicesSpecs($app));

        if ($app->getSteps()->has($this->getAuthStepName())) {
            /** @var Step $step */
            $step = $app->getSteps()->get($this->getAuthStepName());
            $step->plug(function (ApplicationInterface $app) {
                return $this->getConnectResponse($app);
            });
        } else {
            return $this->getConnectResponse($app);
        }
    }

    /**
     * Get the connect response that will redirect to the IDP if this is needed
     *
     * @param ApplicationInterface $app
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function getConnectResponse(ApplicationInterface $app)
    {
        /** @var Connect $connect */
        $connect = $app->getServicesFactory()->get('connect.client');

        if ($connect->getUser() instanceof User) {
            (new Session())->set('connect_user', $connect->getUser());
        }
        $connect->handleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

        return $connect->getResponse();
    }

    /**
     * Register the services used by connect
     *
     * @param ApplicationInterface $app
     *
     * @return array
     * @throws \Exception
     */
    protected function getServicesSpecs(ApplicationInterface $app)
    {
        $config = $app->getConfig();
        $specs = [];

        if ($config->has(ConnectConfig::class)) {
            $directive = $config->get(ConnectConfig::class);

            $setters = [
                'setDefaultTargetPath' => [$directive['defaultTargetPath']],
                'setLogoutTargetPath' => [$directive['logoutTargetPath']],
                'setEntityId' => [$directive['entityId']],
                'setIdpEntityID' => [$directive['idpEntityID']],
                'setName' => [$directive['name'] ?: $directive['entityId']],
                'setSamlMetadataBaseDir' => [$directive['samlMetadataBaseDir']],
                'setSpMetadataFile' => [$directive['spMetadataFile']],
                'setIdpMetadataFile' => [$directive['idpMetadataFile']],
                'setIdpMetadataFileTarget' => [$directive['idpMetadataFileTarget']],
                'setPrivateKeyFilePath' => [$directive['privateKeyFilePath']],
                'setAdminPathInfo' => [$directive['adminPathInfo']],
            ];
            
            $callback = $directive['profileAssociationCallback'];
            if ($callback) {
                $setters['registerProfileAssociation'] = [$callback, $directive['profileAssociationPath']];
            }

            $specs[] = [
                'id' => 'connect.config',
                'class' => Config::class,
                'setters' => $setters
            ];
        } else {
            throw new \Exception(sprintf(
                'Configuration `%s` not found',
                ConnectConfig::class
            ));
        }

        $specs[] = [
            'id' =>'connect.client',
            'class' => Connect::class,
            'params' => [new ServiceReference('connect.config')]
        ];

        return $specs;
    }

    /**
     * Get AuthStep
     *
     * @return string
     */
    public function getAuthStepName(): string
    {
        return $this->authStepName;
    }

    /**
     * Set AuthStep
     *
     * @param string $authStepName
     *
     * @return $this
     */
    public function setAuthStepName(string $authStepName)
    {
        $this->authStepName = $authStepName;

        return $this;
    }
}
