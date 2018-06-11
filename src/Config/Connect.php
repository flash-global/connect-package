<?php

namespace Fei\Service\Connect\Package\Config;

use Fei\Service\Connect\Common\Entity\User;
use ObjectivePHP\Config\Directive\AbstractComplexDirective;

/**
 * Connect configuration object
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect
 */
class Connect extends AbstractComplexDirective
{
    const KEY = 'connect';

    /**
     * @var string
     */
    protected $key = self::KEY;

    /**
     * @config-attribute
     *
     * @var bool
     */
    protected $enable = true;

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $clientServiceId = 'connect.client';

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $configServiceId = 'connect.config';

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $userServiceId = 'connect.user';

    /**
     * @config-attribute hash
     *
     * @config-example-value '{"user_name": "Mock username"}'
     *
     * @var User
     */
    protected $mockUser;

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $defaultTargetPath = '/';

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $logoutTargetPath = '/';

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $profileAssociationPath = '/connect/profile-association';

    /**
     * @config-attribute
     *
     * @config-example-value 'connect.profile-association.service'
     *
     * @var string
     */
    protected $profileAssociationServiceId;

    /**
     * @config-attribute
     *
     * @config-example-value 'http://my-site.com'
     *
     * @var string
     */
    protected $entityId;

    /**
     * @config-attribute
     *
     * @config-example-value 'http://idp.com'
     *
     * @var string
     */
    protected $idpEntityId;

    /**
     * @config-attribute
     *
     * @config-example-value 'My SP name'
     *
     * @var string Entity name (replaced by entityId if not set)
     */
    protected $name;

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $samlMetadataBasedir = 'app/config/Saml/metadata';

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $spMetadataFile = 'sp.xml';

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $idpMetadataFile = 'idp.xml';

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $idpMetadataFileTarget = '/idp.xml';

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $privateKeyFilePath = 'app/config/key/sp.pem';

    /**
     * @config-attribute
     *
     * @var string
     */
    protected $adminPathInfo = '/connect/admin';

    /**
     * @config-attribute
     *
     * @config-example-value '["USER", "ADMIN"]'
     *
     * @var array
     */
    protected $allowedRoles = ['USER'];

    /**
     * @config-attribute
     *
     * @config-example-value '["services.filters.1", "services.filters.1"]'
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Get client status
     *
     * @return bool
     */
    public function enable(): bool
    {
        return $this->enable;
    }

    /**
     * Set enable
     *
     * @param bool $enable
     *
     * @return $this
     */
    public function setEnable(bool $enable)
    {
        $this->enable = $enable;

        return $this;
    }

    /**
     * Get ClientServiceId
     *
     * @return string
     */
    public function getClientServiceId(): string
    {
        return $this->clientServiceId;
    }

    /**
     * Set ClientServiceId
     *
     * @param string $clientServiceId
     *
     * @return $this
     */
    public function setClientServiceId(string $clientServiceId)
    {
        $this->clientServiceId = $clientServiceId;

        return $this;
    }

    /**
     * Get ConfigServiceId
     *
     * @return string
     */
    public function getConfigServiceId(): string
    {
        return $this->configServiceId;
    }

    /**
     * Set ConfigServiceId
     *
     * @param string $configServiceId
     *
     * @return $this
     */
    public function setConfigServiceId(string $configServiceId)
    {
        $this->configServiceId = $configServiceId;

        return $this;
    }

    /**
     * Get UserServiceId
     *
     * @return string
     */
    public function getUserServiceId(): string
    {
        return $this->userServiceId;
    }

    /**
     * Set UserServiceId
     *
     * @param string $userServiceId
     *
     * @return $this
     */
    public function setUserServiceId(string $userServiceId)
    {
        $this->userServiceId = $userServiceId;

        return $this;
    }

    /**
     * Get MockUser
     *
     * @return User
     */
    public function getMockUser(): User
    {
        return $this->mockUser;
    }

    /**
     * Set MockUser
     *
     * @param array $mockUser
     *
     * @return $this
     */
    public function setMockUser(array $mockUser)
    {
        $this->mockUser = new User($mockUser);

        return $this;
    }

    /**
     * Get DefaultTargetPath
     *
     * @return string
     */
    public function getDefaultTargetPath(): string
    {
        return $this->defaultTargetPath;
    }

    /**
     * Set DefaultTargetPath
     *
     * @param string $defaultTargetPath
     *
     * @return $this
     */
    public function setDefaultTargetPath(string $defaultTargetPath)
    {
        $this->defaultTargetPath = $defaultTargetPath;

        return $this;
    }

    /**
     * Get LogoutTargetPath
     *
     * @return string
     */
    public function getLogoutTargetPath(): string
    {
        return $this->logoutTargetPath;
    }

    /**
     * Set LogoutTargetPath
     *
     * @param string $logoutTargetPath
     *
     * @return $this
     */
    public function setLogoutTargetPath(string $logoutTargetPath)
    {
        $this->logoutTargetPath = $logoutTargetPath;

        return $this;
    }

    /**
     * Get ProfileAssociationPath
     *
     * @return string
     */
    public function getProfileAssociationPath(): string
    {
        return $this->profileAssociationPath;
    }

    /**
     * Set ProfileAssociationPath
     *
     * @param string $profileAssociationPath
     *
     * @return $this
     */
    public function setProfileAssociationPath(string $profileAssociationPath)
    {
        $this->profileAssociationPath = $profileAssociationPath;

        return $this;
    }

    /**
     * Get ProfileAssociationServiceId
     *
     * @return string
     */
    public function getProfileAssociationServiceId()
    {
        return $this->profileAssociationServiceId;
    }

    /**
     * Set ProfileAssociationServiceId
     *
     * @param string $profileAssociationServiceId
     *
     * @return $this
     */
    public function setProfileAssociationServiceId(string $profileAssociationServiceId)
    {
        $this->profileAssociationServiceId = $profileAssociationServiceId;

        return $this;
    }

    /**
     * Get EntityId
     *
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * Set EntityId
     *
     * @param string $entityId
     *
     * @return $this
     */
    public function setEntityId(string $entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get IdpEntityId
     *
     * @return string
     */
    public function getIdpEntityId(): string
    {
        return $this->idpEntityId;
    }

    /**
     * Set IdpEntityId
     *
     * @param string $idpEntityId
     *
     * @return $this
     */
    public function setIdpEntityId(string $idpEntityId)
    {
        $this->idpEntityId = $idpEntityId;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get SamlMetadataBasedir
     *
     * @return string
     */
    public function getSamlMetadataBasedir(): string
    {
        return $this->samlMetadataBasedir;
    }

    /**
     * Set SamlMetadataBasedir
     *
     * @param string $samlMetadataBasedir
     *
     * @return $this
     */
    public function setSamlMetadataBasedir(string $samlMetadataBasedir)
    {
        $this->samlMetadataBasedir = $samlMetadataBasedir;

        return $this;
    }

    /**
     * Get SpMetadataFile
     *
     * @return string
     */
    public function getSpMetadataFile(): string
    {
        return $this->spMetadataFile;
    }

    /**
     * Set SpMetadataFile
     *
     * @param string $spMetadataFile
     *
     * @return $this
     */
    public function setSpMetadataFile(string $spMetadataFile)
    {
        $this->spMetadataFile = $spMetadataFile;

        return $this;
    }

    /**
     * Get IdpMetadataFile
     *
     * @return string
     */
    public function getIdpMetadataFile(): string
    {
        return $this->idpMetadataFile;
    }

    /**
     * Set IdpMetadataFile
     *
     * @param string $idpMetadataFile
     *
     * @return $this
     */
    public function setIdpMetadataFile(string $idpMetadataFile)
    {
        $this->idpMetadataFile = $idpMetadataFile;

        return $this;
    }

    /**
     * Get IdpMetadataFileTarget
     *
     * @return string
     */
    public function getIdpMetadataFileTarget(): string
    {
        return $this->idpMetadataFileTarget;
    }

    /**
     * Set IdpMetadataFileTarget
     *
     * @param string $idpMetadataFileTarget
     *
     * @return $this
     */
    public function setIdpMetadataFileTarget(string $idpMetadataFileTarget)
    {
        $this->idpMetadataFileTarget = $idpMetadataFileTarget;

        return $this;
    }

    /**
     * Get PrivateKeyFilePath
     *
     * @return string
     */
    public function getPrivateKeyFilePath(): string
    {
        return $this->privateKeyFilePath;
    }

    /**
     * Set PrivateKeyFilePath
     *
     * @param string $privateKeyFilePath
     *
     * @return $this
     */
    public function setPrivateKeyFilePath(string $privateKeyFilePath)
    {
        $this->privateKeyFilePath = $privateKeyFilePath;

        return $this;
    }

    /**
     * Get AdminPathInfo
     *
     * @return string
     */
    public function getAdminPathInfo(): string
    {
        return $this->adminPathInfo;
    }

    /**
     * Set AdminPathInfo
     *
     * @param string $adminPathInfo
     *
     * @return $this
     */
    public function setAdminPathInfo(string $adminPathInfo)
    {
        $this->adminPathInfo = $adminPathInfo;

        return $this;
    }

    /**
     * Get AllowedRoles
     *
     * @return array
     */
    public function getAllowedRoles(): array
    {
        return $this->allowedRoles;
    }

    /**
     * Set AllowedRoles
     *
     * @param array $allowedRoles
     *
     * @return $this
     */
    public function setAllowedRoles(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;

        return $this;
    }

    /**
     * Get Filters
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Set Filters
     *
     * @param array $filters
     *
     * @return $this
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }
}
