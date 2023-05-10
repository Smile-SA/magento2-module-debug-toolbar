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
            'Number' => [
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
                'nb_call' => 0,
                'size_total' => 0,
                'size_mean' => 0,
                'time_total' => 0,
                'time_mean' => 0,
                'calls' => [],
            ];
        }

        $usage = $this->cacheUsage[$identifier];

        $usage['nb_call']++;
        $usage['size_total'] += $size;
        $usage['time_total'] += $deltaTime;

        $usage['size_mean'] = $usage['size_total'] / $usage['nb_call'];
        $usage['time_mean'] = $usage['time_total'] / $usage['nb_call'];

        $usage['calls'][] = [
            'action' => $action,
            'size' => $size,
            'time' => $deltaTime,
        ];

        $this->cacheUsage[$identifier] = $usage;

        $this->cacheStats['Number']['total']++;
        $this->cacheStats['Time']['total'] += $deltaTime;
        $this->cacheStats['Size']['total'] += $size;

        $this->cacheStats['Number'][$action]++;
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
