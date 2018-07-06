<?php

namespace ObjectivePHP\Package\Connect\Command;

use Fei\Service\Connect\Client\Connect;
use Fei\Service\Connect\Client\MetadataBuilder;
use Fei\Service\Connect\Common\Cryptography\X509CertificateGen;
use League\CLImate\CLImate;
use LightSaml\Credential\X509Certificate;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Cli\Action\AbstractCliAction;
use ObjectivePHP\Cli\Action\Parameter\Param;
use ObjectivePHP\Package\Connect\ConnectPackage;
use ObjectivePHP\ServicesFactory\Specs\InjectionAnnotationProvider;

/**
 * Class InstallCommand
 *
 * @package ObjectivePHP\Package\Connect\Command
 */
class InstallCommand extends AbstractCliAction implements InjectionAnnotationProvider
{
    /**
     * InstallCommand constructor.
     */
    public function __construct()
    {
        $this->setCommand('connect:install');
        $this->setDescription('Install Connect Service Provider instance');

        $this->expects(new Param('acs', 'Acs url for the application (http://your.entityID/acs by default)'));
        $this->expects(new Param('logout', 'Logout url for the application (http://your.entityID/logout by default)'));
    }

    /**
     * {@inheritdoc}
     */
    public function run(ApplicationInterface $app)
    {
        $cli = new CLImate();

        $app->getServicesFactory()->registerService(...(new ConnectPackage())->getServicesSpecs($app));

        /** @var Connect $connect */
        $connect = $this->getServicesFactory()->get('connect.client');

        $entityId = $connect->getConfig()->getEntityID();
        $privateKey = $connect->getConfig()->getPrivateKey();

        $certificateGen = (new X509CertificateGen())->createX509Certificate($privateKey);

        $certificate = new X509Certificate();
        $certificate->loadPem($certificateGen);

        $acs = $this->getParam('acs') ?: $entityId . '/acs';
        $logout = $this->getParam('logout') ?: $entityId . '/logout';

        $builder = new MetadataBuilder();
        $xml = $builder->build($entityId, $acs, $logout, $certificate);

        file_put_contents(
            $connect->getConfig()->getSamlMetadataBaseDir() . '/' . $connect->getConfig()->getSpMetadataFile(),
            $xml
        );

        $cli->info(sprintf('Service Provider "%s" was installed.', $entityId));
        $cli->br();

        $cli->bold('Please note the Service Provider Metadata below:');
        $cli->br();
        $cli->comment($xml);

        return 0;
    }
}
