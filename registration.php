<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Profiler;
use Magento\Framework\Profiler\Driver\Standard\Stat;
use Smile\DebugToolbar\Helper\Profiler as SmileProfiler;

if (PHP_SAPI !== 'cli') {
    // We need to declare the stat profiler manually, to use it after
    $options = [
        'drivers' => [
            [
                'output' => false,
                'stat' => new Stat(),
            ],
        ],
    ];

    Profiler::applyConfig($options, BP, false);
    SmileProfiler::setStat($options['drivers'][0]['stat']);
}

ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Smile_DebugToolbar', __DIR__);
