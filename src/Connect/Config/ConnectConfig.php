<?php

namespace ObjectivePHP\Package\Connect\Config;

use ObjectivePHP\Config\SingleValueDirective;
use ObjectivePHP\ServicesFactory\ServiceReference;

/**
 * Class Config
 *
 * @package ObjectivePHP\Package\Connect\Config
 */
class ConnectConfig extends SingleValueDirective
{
    /**
     * ConnectionConfig constructor.
     *
     * @param array $value
     */
    public function __construct(array $value = [])
    {
        parent::__construct($this->initDefaultValue($value));
    }

    /**
     * Initialize default value
     *
     * @param array $value
     *
     * @return array
     */
    public function initDefaultValue(array $value) : array
    {
        return $value + [
            'defaultTargetPath' => '/',
            'logoutTargetPath' => '/',
            'profileAssociationPath' => '/connect/profile-association',
            'profileAssociationCallback' => null,
            'entityId' => null,
            'idpEntityID' => null,
            'name' => null,
            'samlMetadataBaseDir' => 'app/metadata',
            'spMetadataFile' => 'sp.xml',
            'idpMetadataFile' => 'idp.xml',
            'idpMetadataFileTarget' => 'idp.xml',
            'privateKeyFilePath' => 'app/key/sp.pem',
            'adminPathInfo' => '/connect/admin',
        ];
    }

    /**
     * Get DefaultTargetPath
     *
     * @return string
     */
    public function getDefaultTargetPath()
    {
        return $this->value['defaultTargetPath'];
    }

    /**
     * Set DefaultTargetPath
     *
     * @param string $defaultTargetPath
     *
     * @return $this
     */
    public function setDefaultTargetPath($defaultTargetPath)
    {
        $this->value['defaultTargetPath'] = $defaultTargetPath;

        return $this;
    }

    /**
     * Get LogoutTargetPath
     *
     * @return string
     */
    public function getLogoutTargetPath()
    {
        return $this->value['logoutTargetPath'];
    }

    /**
     * Set LogoutTargetPath
     *
     * @param string $logoutTargetPath
     *
     * @return $this
     */
    public function setLogoutTargetPath($logoutTargetPath)
    {
        $this->value['logoutTargetPath'] = $logoutTargetPath;

        return $this;
    }

    /**
     * Get ProfileAssociationPath
     *
     * @return string
     */
    public function getProfileAssociationPath()
    {
        return $this->value['profileAssociationPath'];
    }

    /**
     * Get ProfileAssociationCallback
     *
     * @return callable
     */
    public function getProfileAssociationCallback()
    {
        return $this->value['profileAssociationCallback'];
    }

    /**
     * Register a profile association callback
     *
     * @param callable|ServiceReference $callback
     * @param string   $profileAssociationPath
     *
     * @return $this
     */
    public function registerProfileAssociation(
        $callback,
        $profileAssociationPath = '/connect/profile-association'
    ) {
        $this->value['profileAssociationCallback'] = $callback;
        $this->value['profileAssociationPath'] = $profileAssociationPath;

        return $this;
    }

    /**
     * Set EntityID
     *
     * @param string $entityID
     *
     * @return $this
     */
    public function setEntityID($entityID)
    {
        $this->value['entityId'] = $entityID;

        return $this;
    }

    /**
     * Set the name of the app
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->value['name'] = $name;

        return $this;
    }

    /**
     * Set IdpEntityID
     *
     * @param string $idpEntityID
     *
     * @return $this
     */
    public function setIdpEntityID($idpEntityID)
    {
        $this->value['idpEntityID'] = $idpEntityID;

        return $this;
    }

    /**
     * Set SamlMetadataBaseDir
     *
     * @param string $samlMetadataBaseDir
     *
     * @return $this
     */
    public function setSamlMetadataBaseDir($samlMetadataBaseDir)
    {
        $this->value['samlMetadataBaseDir'] = $samlMetadataBaseDir;

        return $this;
    }

    /**
     * Set SpMetadataFile
     *
     * @param string $spMetadataFile
     *
     * @return $this
     */
    public function setSpMetadataFile($spMetadataFile)
    {
        $this->value['spMetadataFile'] = $spMetadataFile;

        return $this;
    }

    /**
     * Set IdpMetadataFile
     *
     * @param string $idpMetadataFile
     *
     * @return $this
     */
    public function setIdpMetadataFile($idpMetadataFile)
    {
        $this->value['idpMetadataFile'] = $idpMetadataFile;

        return $this;
    }

    /**
     * @param string $idpMetadataFileTarget
     * @return $this
     */
    public function setIdpMetadataFileTarget($idpMetadataFileTarget)
    {
        $this->value['idpMetadataFileTarget'] = $idpMetadataFileTarget;

        return $this;
    }

    /**
     * Set PrivateKeyFile
     *
     * @param string $privateKeyFilePath
     *
     * @return $this
     */
    public function setPrivateKeyFilePath($privateKeyFilePath)
    {
        $this->value['privateKeyFilePath'] = $privateKeyFilePath;

        return $this;
    }

    /**
     * Set PingPathInfo
     *
     * @param string $adminPathInfo
     *
     * @return $this
     */
    public function setAdminPathInfo($adminPathInfo)
    {
        $this->value['adminPathInfo'] = $adminPathInfo;

        return $this;
    }
}
