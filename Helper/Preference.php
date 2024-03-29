<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Interception\DefinitionInterface;
use Magento\Framework\Interception\ObjectManager\Config\Compiled as ObjectManagerConfigProd;
use Magento\Framework\Interception\ObjectManager\Config\Developer as ObjectManagerConfigDev;
use Magento\Framework\Interception\PluginListInterface as PluginList;
use Magento\Framework\ObjectManager\ConfigInterface as ObjectManagerConfig;
use ReflectionClass;
use ReflectionException;

/**
 * Preference helper.
 */
class Preference extends AbstractHelper
{
    public function __construct(
        Context $context,
        protected PluginList $pluginList,
        protected ObjectManagerConfig $objectManagerConfig
    ) {
        parent::__construct($context);
    }

    /**
     * Get the plugin stats.
     *
     * @throws ReflectionException
     */
    public function getPluginStats(): array
    {
        // Get some properties without rewrite but the properties are private
        $reflectionClass = new ReflectionClass($this->pluginList);

        $property = $reflectionClass->getProperty('_definitions');
        $property->setAccessible(true);
        /** @var DefinitionInterface $definitions */
        $definitions = $property->getValue($this->pluginList);

        $property = $reflectionClass->getProperty('_pluginInstances');
        $property->setAccessible(true);
        $pluginInstances = $property->getValue($this->pluginList);

        ksort($pluginInstances);

        $plugins = [];
        foreach ($pluginInstances as $type => $pluginList) {
            ksort($pluginList);
            foreach ($pluginList as $pluginName => $pluginInstance) {
                $methods = $definitions->getMethodList($pluginInstance);

                foreach ($methods as $method => $methodType) {
                    $methods[$method] = $this->getPluginType((int) $methodType);
                }

                $plugins[] = [
                    'classname' => get_class($pluginInstance),
                    'name' => $pluginName,
                    'method_count' => count($methods),
                    'methods' => $methods,
                    'original_classname' => $type,
                ];
            }
        }

        return $plugins;
    }

    /**
     * Get the plugin type.
     */
    protected function getPluginType(int $methodType): string
    {
        return match ($methodType) {
            DefinitionInterface::LISTENER_AROUND => 'around',
            DefinitionInterface::LISTENER_BEFORE => 'before',
            DefinitionInterface::LISTENER_AFTER => 'after',
            default => 'unknown',
        };
    }

    /**
     * Get the preference stats.
     *
     * @throws ReflectionException
     */
    public function getPreferenceStats(): array
    {
        $preferences = [];

        $config = $this->objectManagerConfig;
        $reflectionClass = new ReflectionClass($config);
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
