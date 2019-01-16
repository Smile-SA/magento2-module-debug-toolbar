<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Interception\DefinitionInterface;
use Magento\Framework\Interception\ObjectManager\Config\Compiled as ObjectManagerConfigProd;
use Magento\Framework\Interception\ObjectManager\Config\Developer as ObjectManagerConfigDev;
use Magento\Framework\Interception\PluginListInterface as PluginList;
use Magento\Framework\ObjectManager\ConfigInterface as ObjectManagerConfig;

/**
 * Helper: Preference
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Preference extends AbstractHelper
{
    /**
     * @var PluginList
     */
    protected $pluginList;

    /**
     * @var ObjectManagerConfig
     */
    protected $objectManagerConfig;

    /**
     * @param Context $context
     * @param PluginList $pluginList
     * @param ObjectManagerConfig $objectManagerConfig
     */
    public function __construct(
        Context $context,
        PluginList $pluginList,
        ObjectManagerConfig $objectManagerConfig
    ) {
        parent::__construct($context);

        $this->pluginList = $pluginList;
        $this->objectManagerConfig = $objectManagerConfig;
    }

    /**
     * Get the plugin stats.
     *
     * @return array
     */
    public function getPluginStats()
    {
        // Get some properties without rewrite but the properties are private
        $reflectionClass = new \ReflectionClass($this->pluginList);

        /** @var DefinitionInterface $definitions */
        $property = $reflectionClass->getProperty('_definitions');
        $property->setAccessible(true);
        $definitions = $property->getValue($this->pluginList);

        $property = $reflectionClass->getProperty('_pluginInstances');
        $property->setAccessible(true);
        $pluginInstances = $property->getValue($this->pluginList);

        ksort($pluginInstances);

        $plugins = [];
        foreach ($pluginInstances as $originalClassname => $pluginList) {
            ksort($pluginList);
            foreach ($pluginList as $pluginName => $pluginInstance) {
                $methods = $definitions->getMethodList($pluginInstance);

                foreach ($methods as $method => $methodType) {
                    $methods[$method] = $this->getPluginType($methodType);
                }

                $plugins[] = [
                    'main_classname' => $originalClassname,
                    'nb_plugins' => count($pluginList),
                    'plugin_name' => $pluginName,
                    'plugin_classname' => get_class($pluginInstance),
                    'plugin_nb_methods' => count($methods),
                    'plugin_methods' => $methods,
                ];
            }
        }

        return $plugins;
    }

    /**
     * Get the plugin type.
     *
     * @param int $methodType
     * @return string
     */
    protected function getPluginType($methodType)
    {
        switch ($methodType) {
            case DefinitionInterface::LISTENER_AROUND:
                return 'Around';

            case DefinitionInterface::LISTENER_BEFORE:
                return 'Before';

            case DefinitionInterface::LISTENER_AFTER:
                return 'After';
        }

        return 'Unknown [' . $methodType . ']';
    }

    /**
     * Get the preference stats.
     *
     * @return array
     */
    public function getPreferenceStats()
    {
        $preferences = [];

        $config = $this->objectManagerConfig;
        $reflectionClass = new \ReflectionClass($config);
        $property = null;

        /**
         * The Object Manager config has 2 implementations :
         *
         * - ObjectManagerConfigProd with the private `preferences` property
         * - ObjectManagerConfigDev with the private `_preferences` property
         *
         * => We must test the classname to get the good property, and use reflection to access to the property
         */
        if ($config instanceof ObjectManagerConfigProd) {
            /** @var ObjectManagerConfigProd $config */
            $property = $reflectionClass->getParentClass()->getProperty('preferences');
        }

        if ($config instanceof ObjectManagerConfigDev) {
            /** @var ObjectManagerConfigDev $config */
            $property = $reflectionClass->getProperty('_preferences');
        }

        if ($property !== null) {
            $property->setAccessible(true);

            $preferences = $property->getValue($config);
            ksort($preferences);
        }

        return $preferences;
    }
}
