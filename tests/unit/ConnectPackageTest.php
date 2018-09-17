<?php

namespace Tests\Fei\Connect\Package\Injector;

use Fei\Service\Connect\Common\Entity\User;
use Fei\Service\Connect\Package\Config\Connect;
use Fei\Service\Connect\Package\ConnectPackage;
use Fei\Service\Connect\Package\Injector\ConnectAwareInjector;
use Fei\Service\Connect\Package\Injector\UserAwareInjector;
use Fei\Service\Connect\Package\Middleware\AllowedRolesMiddleware;
use Fei\Service\Connect\Package\Middleware\ConnectMiddleware;
use ObjectivePHP\Application\AbstractHttpApplication;
use ObjectivePHP\Application\Middleware\MiddlewareRegistry;
use ObjectivePHP\Application\Workflow\WorkflowEvent;
use ObjectivePHP\Config\Config;
use ObjectivePHP\Filter\FilterInterface;
use ObjectivePHP\ServicesFactory\ServicesFactory;
use PHPUnit\Framework\TestCase;
use Fei\Service\Connect\Package\Config\Connect as ConnectDirective;

/**
 * Class ConnectPackage
 *
 * @package Tests\Fei\Connect\Package\Injector
 */
class ConnectPackageTest extends TestCase
{
    public function testGetConfig()
    {
        $package = new ConnectPackage();

        $this->assertEquals((new Config())->registerDirective(new ConnectDirective()), $package->getConfig());
    }

    public function testConnectIsWellConstructed()
    {
        $connectConfig = new ConnectDirective();

        $connectConfig->setEnable(true);
        $connectConfig->setClientServiceId('test.client');
        $connectConfig->setConfigServiceId('test.config');
        $connectConfig->setUserServiceId('test.user');
        $connectConfig->setMockUser(['username' => 'test']);
        $connectConfig->setDefaultTargetPath('/default');
        $connectConfig->setLogoutTargetPath('/logout/target');
        $connectConfig->setProfileAssociationPath('/connect/profile');
        $connectConfig->setProfileAssociationServiceId('test.profile');
        $connectConfig->setEntityId('http://sp.com');
        $connectConfig->setIdpEntityId('http://idp.com');
        $connectConfig->setName('This is a test');
        $connectConfig->setSamlMetadataBasedir(__DIR__ . '/../_output/saml');
        $connectConfig->setSpMetadataFile('sp.test.xml');
        $connectConfig->setIdpMetadataFile('idp.test.xml');
        $connectConfig->setIdpMetadataFileTarget('/idp.test.xml');
        $connectConfig->setPrivateKeyFilePath(__DIR__ . '/../_output/key/key.pem');
        $connectConfig->setAdminPathInfo('/test/admin');
        $connectConfig->setAllowedRoles(['TEST', 'ADMIN_TEST']);
        $connectConfig->setFilters(['service.filter.1', 'service.filter.2']);

        $config = new Config();
        $config->registerDirective(new ConnectDirective());

        $config->hydrate([
            'connect' => [
                'enable' => true,
                'client_service_id' => 'test.client',
                'config_service_id' => 'test.config',
                'user_service_id' => 'test.user',
                'mock_user' => ['username' => 'test'],
                'default_target_path' => '/default',
                'logout_target_path' => '/logout/target',
                'profile_association_path' => '/connect/profile',
                'profile_association_service_id' => 'test.profile',
                'entity_id' => 'http://sp.com',
                'idp_entity_id' => 'http://idp.com',
                'name' => 'This is a test',
                'saml_metadata_basedir' => __DIR__ . '/../_output/saml',
                'sp_metadata_file' => 'sp.test.xml',
                'idp_metadata_file' => 'idp.test.xml',
                'idp_metadata_file_target' => '/idp.test.xml',
                'private_key_file_path' => __DIR__ . '/../_output/key/key.pem',
                'admin_path_info' => '/test/admin',
                'allowed_roles' => ['TEST', 'ADMIN_TEST'],
                'filters' => ['service.filter.1', 'service.filter.2']
            ]
        ]);

        $this->assertEquals($connectConfig, $config->get(ConnectDirective::KEY));

        $servicesFactory = new ServicesFactory();
        $servicesFactory->setConfig($config);
        $servicesFactory->registerService(
            [
                'id' => 'service.filter.1',
                'instance' => new class implements FilterInterface {
                    public function filter(...$value): bool
                    {
                        return true;
                    }
                }
            ],
            [
                'id' => 'service.filter.2',
                'instance' => new class implements FilterInterface {
                    public function filter(...$value): bool
                    {
                        return true;
                    }
                }
            ],
            [
                'id' => 'test.profile',
                'factory' => function ($id, ServicesFactory $servicesFactory) {
                    return function () {
                    };
                }
            ]
        );

        $middlewareRegistry = new MiddlewareRegistry();

        $application = $this->getMockBuilder(AbstractHttpApplication::class)
            ->setMethods(['getConfig', 'getServicesFactory', 'getMiddlewares'])
            ->getMockForAbstractClass();
        $application->expects($this->once())->method('getConfig')->willReturn($config);
        $application->expects($this->any())->method('getServicesFactory')->willReturn($servicesFactory);
        $application->expects($this->any())->method('getMiddlewares')->willReturn($middlewareRegistry);

        $event = new WorkflowEvent($application);

        $package = new ConnectPackage();

        $package->onPackagesReady($event);

        $this->assertTrue($application->getServicesFactory()->has($connectConfig->getClientServiceId()));
        $this->assertTrue($application->getServicesFactory()->has($connectConfig->getUserServiceId()));
        $this->assertTrue($application->getServicesFactory()->has($connectConfig->getConfigServiceId()));

        $expectedMiddleware = new ConnectMiddleware();
        $expectedMiddleware->registerFilter($servicesFactory->get('service.filter.1'));
        $expectedMiddleware->registerFilter($servicesFactory->get('service.filter.2'));

        $this->assertEquals(
            (new MiddlewareRegistry())
                ->registerMiddleware($expectedMiddleware)
                ->registerMiddleware(new AllowedRolesMiddleware($connectConfig->getAllowedRoles())),
            $application->getMiddlewares()
        );

        $this->assertTrue(
            in_array(
                new UserAwareInjector($connectConfig->getUserServiceId()),
                $servicesFactory->getInjectors()->toArray()
            )
        );

        $this->assertTrue(
            in_array(
                new ConnectAwareInjector($connectConfig->getClientServiceId()),
                $servicesFactory->getInjectors()->toArray()
            )
        );
    }

    public function testDisabled()
    {
        $config = new Config();
        $config->registerDirective(new ConnectDirective());

        $config->hydrate([
            'connect' => [
                'enable' => false,
                'entity_id' => 'http://sp.com',
                'idp_entity_id' => 'http://idp.com',
                'mock_user' => ['username' => 'test'],
            ]
        ]);

        /** @var ConnectDirective $connectConfig */
        $connectConfig = $config->get(ConnectDirective::KEY);

        $servicesFactory = new ServicesFactory();

        $middlewareRegistry = new MiddlewareRegistry();

        $application = $this->getMockBuilder(AbstractHttpApplication::class)
            ->setMethods(['getConfig', 'getServicesFactory', 'getMiddlewares'])
            ->getMockForAbstractClass();
        $application->expects($this->once())->method('getConfig')->willReturn($config);
        $application->expects($this->any())->method('getServicesFactory')->willReturn($servicesFactory);
        $application->expects($this->any())->method('getMiddlewares')->willReturn($middlewareRegistry);

        $event = new WorkflowEvent($application);

        $package = new ConnectPackage();

        $package->onPackagesReady($event);

        $this->assertTrue($application->getServicesFactory()->has($connectConfig->getClientServiceId()));
        $this->assertTrue($application->getServicesFactory()->has($connectConfig->getUserServiceId()));
        $this->assertTrue($application->getServicesFactory()->has($connectConfig->getConfigServiceId()));

        $this->assertEquals(new MiddlewareRegistry(), $application->getMiddlewares());

        $this->assertEquals(
            (new User())->setUserName('test')->setCreatedAt(new \DateTime('1984/10/11 10:00:00')),
            $servicesFactory->get($connectConfig->getUserServiceId())
        );
    }

    public function testMiddlewareAreFiltered()
    {
        $config = new Config();
        $config->registerDirective(new ConnectDirective());



        $config->hydrate([
            'connect' => [
                'enable' => true,
                'mock_user' => ['username' => 'test'],
                'entity_id' => 'http://sp.com',
                'idp_entity_id' => 'http://idp.com',
                'filters' => ['service.filter.1', 'service.filter.2']
            ]
        ]);

        $connectConfig = $config->get(ConnectDirective::KEY);

        $this->assertEquals($connectConfig, $config->get(ConnectDirective::KEY));

        $servicesFactory = new ServicesFactory();
        $servicesFactory->setConfig($config);
        $servicesFactory->registerService(
            [
                'id' => 'service.filter.1',
                'instance' => new class implements FilterInterface {
                    public function filter(...$value): bool
                    {
                        return false;
                    }
                }
            ],
            [
                'id' => 'service.filter.2',
                'instance' => new class implements FilterInterface {
                    public function filter(...$value): bool
                    {
                        return true;
                    }
                }
            ]
        );

        $middlewareRegistry = new MiddlewareRegistry();

        $application = $this->getMockBuilder(AbstractHttpApplication::class)
            ->setMethods(['getConfig', 'getServicesFactory', 'getMiddlewares'])
            ->getMockForAbstractClass();
        $application->expects($this->once())->method('getConfig')->willReturn($config);
        $application->expects($this->any())->method('getServicesFactory')->willReturn($servicesFactory);
        $application->expects($this->any())->method('getMiddlewares')->willReturn($middlewareRegistry);

        $event = new WorkflowEvent($application);

        $package = new ConnectPackage();

        $package->onPackagesReady($event);

        $this->assertTrue($application->getServicesFactory()->has($connectConfig->getClientServiceId()));
        $this->assertTrue($application->getServicesFactory()->has($connectConfig->getUserServiceId()));
        $this->assertTrue($application->getServicesFactory()->has($connectConfig->getConfigServiceId()));

        $this->assertEquals(new MiddlewareRegistry(), $application->getMiddlewares());
    }
}
