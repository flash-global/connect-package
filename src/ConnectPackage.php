<?php

namespace Fei\Service\Connect\Package;

use Fei\Service\Connect\Client\Connect;
use Fei\Service\Connect\Client\Config\Config as ConnectConfig;
use Fei\Service\Connect\Package\Config\Connect as ConnectDirective;
use Fei\Service\Connect\Package\Injector\ConnectAwareInjector;
use Fei\Service\Connect\Package\Injector\UserAwareInjector;
use Fei\Service\Connect\Package\Middleware\AllowedRolesMiddleware;
use Fei\Service\Connect\Package\Middleware\ConnectMiddleware;
use ObjectivePHP\Application\HttpApplicationInterface;
use ObjectivePHP\Application\Middleware\MiddlewareRegistry;
use ObjectivePHP\Application\Package\PackageInterface;
use ObjectivePHP\Application\Workflow\PackagesReadyListener;
use ObjectivePHP\Application\Workflow\WorkflowEventInterface;
use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\ConfigAccessorsTrait;
use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\ConfigProviderInterface;
use ObjectivePHP\ServicesFactory\ServicesFactory;

/**
 * Class ConnectPackage
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect
 */
class ConnectPackage implements ConfigProviderInterface, PackageInterface, PackagesReadyListener
{
    use ConfigAccessorsTrait;

    public function getConfig(): ConfigInterface
    {
        $config = new Config();

        $config->registerDirective(new ConnectDirective());

        return $config;
    }

    public function onPackagesReady(WorkflowEventInterface $event)
    {
        /**
         * @var ConnectDirective $config
         */
        $config = $event->getApplication()->getConfig()->get(ConnectDirective::KEY);

        $event->getApplication()->getServicesFactory()->registerInjector(
            new ConnectAwareInjector($config->getClientServiceId())
        );
        $event->getApplication()->getServicesFactory()->registerInjector(
            new UserAwareInjector($config->getUserServiceId())
        );

        $setters = [
            'setDefaultTargetPath' => $config->getDefaultTargetPath(),
            'setLogoutTargetPath' => $config->getLogoutTargetPath(),
            'setEntityId' => $config->getEntityId(),
            'setIdpEntityID' => $config->getIdpEntityId(),
            'setName' => $config->getName() ?: $config->getEntityId(),
            'setSamlMetadataBaseDir' => $config->getSamlMetadataBasedir(),
            'setSpMetadataFile' => $config->getSpMetadataFile(),
            'setIdpMetadataFile' => $config->getIdpMetadataFile(),
            'setIdpMetadataFileTarget' => $config->getIdpMetadataFileTarget(),
            'setPrivateKeyFilePath' => $config->getPrivateKeyFilePath(),
            'setAdminPathInfo' => $config->getAdminPathInfo()
        ];

        if ($config->getProfileAssociationServiceId()) {
            $setters['registerProfileAssociation'] = [
                $event->getApplication()->getServicesFactory()->get($config->getProfileAssociationServiceId()),
                $config->getProfileAssociationPath()
            ];
        }

        $event->getApplication()->getServicesFactory()->registerService(
            [
                'id' => $config->getConfigServiceId(),
                'class' => ConnectConfig::class,
                'setters' => $setters
            ]
        );

        $event->getApplication()->getServicesFactory()->registerService(
            [
                'id' => $config->getClientServiceId(),
                'class' => Connect::class,
                'params' => [sprintf('service(%s)', $config->getConfigServiceId())],
                'setters' => $config->enable() ? [] : ['setUser' => $config->getMockUser()]
            ]
        );

        $event->getApplication()->getServicesFactory()->registerService(
            [
                'id' => $config->getUserServiceId(),
                'factory' => function ($id, ServicesFactory $servicesFactory) use ($config) {
                    return $config->enable()
                        ? $servicesFactory->get($config->getClientServiceId())->getUser()
                        : $config->getMockUser();
                }
            ]
        );

        $application = $event->getApplication();

        if ($application instanceof HttpApplicationInterface && $config->enable()) {
            $middleware = new ConnectMiddleware();

            foreach ($config->getFilters() as $service) {
                $middleware->registerFilter($application->getServicesFactory()->get($service));
            }

            if ($middleware->runFilters($event->getApplication())) {
                $application->getMiddlewares()->registerMiddleware(
                    new AllowedRolesMiddleware($config->getAllowedRoles()),
                    MiddlewareRegistry::FIRST
                );

                $application->getMiddlewares()->registerMiddleware($middleware, MiddlewareRegistry::FIRST);
            }
        }
    }
}
