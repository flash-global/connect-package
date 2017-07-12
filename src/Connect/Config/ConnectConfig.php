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
            'profileAssociationCallback' => null
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
}
