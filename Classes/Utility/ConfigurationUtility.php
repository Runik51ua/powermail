<?php
declare(strict_types=1);
namespace In2code\Powermail\Utility;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Class ConfigurationUtility
 */
class ConfigurationUtility extends AbstractUtility
{

    /**
     * Check if disableIpLog is active
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isDisableIpLogActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disableIpLog'] === true;
    }

    /**
     * Check if disableMarketingInformation is active
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isDisableMarketingInformationActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disableMarketingInformation'] === true;
    }

    /**
     * Check if disableBackendModule is active
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isDisableBackendModuleActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disableBackendModule'] === true;
    }

    /**
     * Check if disablePluginInformation is active
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isDisablePluginInformationActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disablePluginInformation'] === true;
    }

    /**
     * Check if disablePluginInformationMailPreview is active
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isDisablePluginInformationMailPreviewActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['disablePluginInformationMailPreview'] === true;
    }

    /**
     * Check if enableCaching is active
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isEnableCachingActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['enableCaching'] === true;
    }

    /**
     * Check if replaceIrreWithElementBrowser is active
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isReplaceIrreWithElementBrowserActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['replaceIrreWithElementBrowser'] === true;
    }

    /**
     * Check if l10n_mode_merge is active
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isL10nModeMergeActive(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return (bool)$extensionConfig['l10n_mode_merge'] === true;
    }

    /**
     * @return array
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function getExtensionConfiguration(): array
    {
        return parent::getExtensionConfiguration();
    }

    /**
     * Get development email (only if in dev context)
     *
     * @return string
     * @codeCoverageIgnore
     */
    public static function getDevelopmentContextEmail(): string
    {

        $configVariables = self::getTypo3ConfigurationVariables();
        if (Environment::getContext()->isDevelopment() &&
            GeneralUtility::validEmail($configVariables['EXT']['powermailDevelopContextEmail'])) {
            return $configVariables['EXT']['powermailDevelopContextEmail'];
        }
        return '';
    }

    /**
     * Get default mail from install tool settings
     *
     * @param string $fallback
     * @return string
     */
    public static function getDefaultMailFromAddress(string $fallback = null): string
    {
        $configVariables = self::getTypo3ConfigurationVariables();
        if (!empty($configVariables['MAIL']['defaultMailFromAddress'])) {
            return $configVariables['MAIL']['defaultMailFromAddress'];
        }
        if ($fallback !== null) {
            return $fallback;
        }
        return '';
    }

    /**
     * Get default mail-name from install tool settings
     *
     * @return string
     */
    public static function getDefaultMailFromName(): string
    {
        $configVariables = self::getTypo3ConfigurationVariables();
        if (!empty($configVariables['MAIL']['defaultMailFromName'])) {
            return $configVariables['MAIL']['defaultMailFromName'];
        }
        return '';
    }

    /**
     * Get path to an icon for TCA configuration
     *
     * @param string $fileName
     * @return string
     */
    public static function getIconPath(string $fileName): string
    {
        return 'EXT:powermail/Resources/Public/Icons/' . $fileName;
    }

    /**
     * Check if a given validation is turned on generally
     * and if there is a given spamshield method enabled
     *
     * @param array $settings
     * @param string $className
     * @return bool
     */
    public static function isValidationEnabled(array $settings, string $className): bool
    {
        $validationActivated = false;
        foreach ((array)$settings['spamshield']['methods'] as $method) {
            if ($method['class'] === $className && $method['_enable'] === '1') {
                $validationActivated = true;
                break;
            }
        }
        return !empty($settings['spamshield']['_enable']) && $validationActivated;
    }

    /**
     * Check if gdlib is loaded on this server
     *
     * @codeCoverageIgnore
     */
    public static function testGdExtension(): void
    {
        if (!extension_loaded('gd')) {
            throw new \InvalidArgumentException('PHP extension gd not loaded.', 1514819369374);
        }
    }

    /**
     * Merges Flexform, TypoScript and Extension Manager Settings
     * Note: If FF value is empty, we want the TypoScript value instead
     *
     * @param array $settings All settings
     * @param string $typoScriptLevel Startpoint
     * @return void
     */
    public static function mergeTypoScript2FlexForm(array &$settings, string $typoScriptLevel = 'setup')
    {
        $settings = ArrayUtility::arrayMergeRecursiveOverrule(
            (array)$settings[$typoScriptLevel],
            (array)$settings['flexform'],
            false,
            false
        );
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     * @codeCoverageIgnore
     */
    public static function isDatabaseConnectionAvailable(): bool
    {
        return !empty($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']);
    }
}
