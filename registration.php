<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */

use Magento\Framework\Component\ComponentRegistrar;

if (PHP_SAPI !== 'cli') {
    // We need to declare the stat profiler manually, to use it after
    $options = [
        'drivers' => [
            [
                'output' => false,
                'stat' => new \Magento\Framework\Profiler\Driver\Standard\Stat(),
            ],
        ],
    ];

    \Magento\Framework\Profiler::applyConfig($options, BP, false);
    \Smile\DebugToolbar\Helper\Profiler::setStat($options['drivers'][0]['stat']);
}

ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Smile_DebugToolbar', __DIR__);
