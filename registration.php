<?php
/**
 * Register the module and the profiler
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
if (PHP_SAPI !== 'cli') {
    // we need to declare the stat profiler manually, to use it after
    $options = [
        'drivers' => [
            [
                'output' => false,
                'stat'   => new \Magento\Framework\Profiler\Driver\Standard\Stat(),
            ]
        ]
    ];

    \Magento\Framework\Profiler::applyConfig($options, BP, false);
    \Smile\DebugToolbar\Helper\Profiler::setStat($options['drivers'][0]['stat']);
}

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Smile_DebugToolbar',
    __DIR__
);
