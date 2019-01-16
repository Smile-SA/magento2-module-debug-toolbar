<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;

/**
 * Helper: Cache
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Cache extends AbstractHelper
{
    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var array
     */
    protected $cacheTypes;

    /**
     * @var array
     */
    protected $cacheUsage = [];

    /**
     * @var array
     */
    protected $cacheStats = [];

    /**
     * @param Context $context
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(Context $context, TypeListInterface $cacheTypeList)
    {
        parent::__construct($context);

        $this->cacheTypeList = $cacheTypeList;
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
     *
     * @param string $action
     * @param string $identifier
     * @param float $deltaTime
     * @param int $size
     */
    public function addStat($action, $identifier, $deltaTime = 0., $size = 0)
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
     *
     * @return array
     */
    public function getCacheUsage()
    {
        return $this->cacheUsage;
    }

    /**
     * Get the cache usage per action.
     *
     * @return array
     */
    public function getStatsPerAction()
    {
        return $this->cacheStats;
    }

    /**
     * Get the cache types.
     *
     * @return array
     */
    public function getCacheTypes()
    {
        if ($this->cacheTypes === null) {
            $this->cacheTypes = [];

            $invalidated = $this->cacheTypeList->getInvalidated();

            /** @var DataObject $items */
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
