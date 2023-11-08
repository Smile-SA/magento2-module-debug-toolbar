<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;

/**
 * Cache helper.
 */
class Cache extends AbstractHelper
{
    protected ?array $cacheTypes = null;
    protected array $cacheUsage = [];
    protected array $cacheStats = [];

    public function __construct(Context $context, protected TypeListInterface $cacheTypeList)
    {
        parent::__construct($context);
        $this->cacheUsage = [];
        $this->cacheStats = [
            'Count' => [
                'total' => 0,
                'load' => 0,
                'save' => 0,
                'remove' => 0,
            ],
            'Time' => [
                'total' => 0,
                'load' => 0,
                'save' => 0,
                'remove' => 0,
            ],
            'Size' => [
                'total' => 0,
                'load' => 0,
                'save' => 0,
                'remove' => 0,
            ],
        ];
    }

    /**
     * Add a stat on cache usage.
     */
    public function addStat(string $action, string $identifier, float $deltaTime = 0., int $size = 0): void
    {
        if (!array_key_exists($identifier, $this->cacheUsage)) {
            $this->cacheUsage[$identifier] = [
                'identifier' => $identifier,
                'call_count' => 0,
                'total_size' => 0,
                'mean_size' => 0,
                'total_time' => 0,
                'mean_time' => 0,
                'calls' => [],
            ];
        }

        $usage = $this->cacheUsage[$identifier];

        $usage['call_count']++;
        $usage['total_size'] += $size;
        $usage['total_time'] += $deltaTime;

        $usage['mean_size'] = $usage['total_size'] / $usage['call_count'];
        $usage['mean_time'] = $usage['total_time'] / $usage['call_count'];

        $usage['calls'][] = [
            'action' => $action,
            'size' => $size,
            'time' => $deltaTime,
        ];

        $this->cacheUsage[$identifier] = $usage;

        $this->cacheStats['Count']['total']++;
        $this->cacheStats['Time']['total'] += $deltaTime;
        $this->cacheStats['Size']['total'] += $size;

        $this->cacheStats['Count'][$action]++;
        $this->cacheStats['Time'][$action] += $deltaTime;
        $this->cacheStats['Size'][$action] += $size;
    }

    /**
     * Get the cache usage.
     */
    public function getCacheUsage(): array
    {
        return $this->cacheUsage;
    }

    /**
     * Get the cache usage per action.
     */
    public function getStatsPerAction(): array
    {
        return $this->cacheStats;
    }

    /**
     * Get the cache types.
     */
    public function getCacheTypes(): array
    {
        if ($this->cacheTypes === null) {
            $this->cacheTypes = [];

            $invalidated = $this->cacheTypeList->getInvalidated();

            /** @var DataObject[] $items */
            $items = $this->cacheTypeList->getTypes();

            foreach ($items as $item) {
                $status = ($item->getData('status') ? 'Enabled' : 'Disabled');

                if (array_key_exists($item->getData('id'), $invalidated)) {
                    $status = 'Invalidated';
                }

                $this->cacheTypes[$item->getData('cache_type')] = $status;
            }
        }

        return $this->cacheTypes;
    }
}
