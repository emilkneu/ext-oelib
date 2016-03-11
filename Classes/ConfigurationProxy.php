<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * This singleton class provides access to an extension's global configuration
 * and allows to fake global configuration values for testing purposes.
 *
 *
 * @author Saskia Metzler <saskia@merlin.owl.de>
 */
class Tx_Oelib_ConfigurationProxy extends Tx_Oelib_PublicObject
{
    /**
     * @var Tx_Oelib_ConfigurationProxy[] the singleton configuration proxy objects
     */
    private static $instances = array();

    /**
     * @var array stored configuration data for each extension which currently
     *            uses the configuration proxy
     */
    private $configuration = array();

    /**
     * @var string key of the extension for which the EM configuration is stored
     */
    private $extensionKey = '';

    /**
     * @var bool whether the configuration is already loaded
     */
    private $isConfigurationLoaded = false;

    /**
     * Don't call this constructor; use getInstance instead.
     *
     * @param string $extensionKey
     *        extension key without the 'tx' prefix, used to retrieve the EM
     *        configuration and as identifier for an extension's instance of
     *        this class, must not be empty
     */
    private function __construct($extensionKey)
    {
        $this->extensionKey = $extensionKey;
    }

    /**
     * Frees as much memory that has been used by this object as possible.
     */
    public function __destruct()
    {
    }

    /**
     * Retrieves the singleton configuration proxy instance for the extension
     * named $extensionKey. This function usually should be called statically.
     *
     * @param string $extensionKey
     *        extension key without the 'tx' prefix, used to retrieve the EM
     *        configuration and as identifier for an extension's instance of
     *        this class, must not be empty
     *
     * @return Tx_Oelib_ConfigurationProxy the singleton configuration
     *                                     proxy object
     */
    public static function getInstance($extensionKey)
    {
        if ($extensionKey === '') {
            throw new InvalidArgumentException('The extension key was not set.', 1331318826);
        }

        if (!is_object(self::$instances[$extensionKey])) {
            self::$instances[$extensionKey]
                = new Tx_Oelib_ConfigurationProxy($extensionKey);
        }

        return self::$instances[$extensionKey];
    }

    /**
     * Purges the current instances so that getInstance will create new instances.
     *
     * @return void
     */
    public static function purgeInstances()
    {
        self::$instances = array();
    }

    /**
     * Loads the EM configuration for the extension key passed via
     * getInstance() if the configuration is not yet loaded.
     *
     * @return void
     */
    private function loadConfigurationLazily()
    {
        if (!$this->isConfigurationLoaded) {
            $this->retrieveConfiguration();
        }
    }

    /**
     * Retrieves the EM configuration for the extension key passed via
     * getInstance().
     *
     * This function is accessible for testing purposes. As lazy implementation
     * is used, this function might be useful to ensure static test conditions.
     *
     * @return void
     */
    public function retrieveConfiguration()
    {
        $this->configuration = unserialize(
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extensionKey]
        );
        $this->isConfigurationLoaded = true;
    }

    /**
     * Checks whether a certain key exists in an extension's configuration.
     *
     * @param string $key
     *        key to check, must not be empty
     *
     * @return bool whether $key occurs in the configuration array of
     *                 the extension named $this->extensionKey
     */
    private function hasConfigurationValue($key)
    {
        $this->loadConfigurationLazily();

        return isset($this->configuration[$key]);
    }

    /**
     * Returns a string configuration value.
     *
     * @param string $key
     *        key of the value to get, must not be empty
     *
     * @return string configuration value string, might be empty
     */
    protected function get($key)
    {
        $this->loadConfigurationLazily();

        if ($this->hasConfigurationValue($key)) {
            $result = $this->configuration[$key];
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * Sets a new configuration value.
     *
     * The configuration setters are intended to be used for testing purposes
     * only.
     *
     * @param string $key
     *        key of the value to set, must not be empty
     * @param mixed $value
     *        the value to set
     *
     * @return void
     */
    protected function set($key, $value)
    {
        $this->loadConfigurationLazily();

        $this->configuration[$key] = $value;
    }

    /**
     * Returns an extension's complete configuration.
     *
     * @return array|bool an extension's configuration, empty if the configuration was not retrieved before
     */
    public function getCompleteConfiguration()
    {
        $this->loadConfigurationLazily();

        return $this->configuration;
    }
}
