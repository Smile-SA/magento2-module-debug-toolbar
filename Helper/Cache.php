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
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\DataObject;

/**
 * Helper: Cache
 *
 * @package   Smile\DebugToolbar\Helper
 * @copyright 2017 Smile
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
     * Cache constructor.
     *
     * @param Context           $context
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context           $context,
        TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);

        $this->cacheTypeList          = $cacheTypeList;
        $this->cacheUsage = [];
        $this->cacheStats = [
            'Number' => [
                'total'  => 0,
                'load'   => 0,
                'save'   => 0,
                'remove' => 0,
            ],
            'Time' => [
                'total'  => 0,
                'load'   => 0,
                'save'   => 0,
                'remove' => 0,
            ],
            'Size' => [
                'total'  => 0,
                'load'   => 0,
                'save'   => 0,
                'remove' => 0,
            ],
        ];
    }

    /**
     * Add a stat on cache usage
     *
     * @param string $action
     * @param string $identifier
     * @param int    $time
     * @param int    $size
     *
     * @return void
     */
    public function addStat($action, $identifier, $time = 0, $size = 0)
    {
        if (!array_key_exists($identifier, $this->cacheUsage)) {
            $this->cacheUsage[$identifier] = [
                'identifier' => $identifier,
                'nb_call'    => 0,
                'size_total' => 0,
                'size_mean'  => 0,
                'time_total' => 0,
                'time_mean'  => 0,
                'calls'      => [],
            ];
        }

        $usage = $this->cacheUsage[$identifier];

        $usage['nb_call']++;
        $usage['size_total']+=$size;
        $usage['time_total']+=$time;

        $usage['size_mean'] = $usage['size_total'] / $usage['nb_call'];
        $usage['time_mean'] = $usage['time_total'] / $usage['nb_call'];

        $usage['calls'][] = [
            'action' => $action,
            'size'   => $size,
            'time'   => $time,
        ];

        $this->cacheUsage[$identifier] = $usage;

        $this->cacheStats['Number']['total']++;
        $this->cacheStats['Time']['total']+= $time;
        $this->cacheStats['Size']['total']+= $size;

        $this->cacheStats['Number'][$action]++;
        $this->cacheStats['Time'][$action]+= $time;
        $this->cacheStats['Size'][$action]+= $size;
    }

    /**
     * Get the cache usage
     *
     * @return array
     */
    public function getCacheUsage()
    {
        return $this->cacheUsage;
    }

    /**
     * Get the cache usage per actions
     *
     * @return array
     */
    public function getStatsPerAction()
    {
        return $this->cacheStats;
    }

    /**
     * Get the cache types
     *
     * @return array
     */
    public function getCacheTypes()
    {
        if (is_null($this->cacheTypes)) {
            $this->cacheTypes = [];

            $invalidated = $this->cacheTypeList->getInvalidated();

            /** @var DataObject $items */
            $items = $this->cacheTypeList->getTypes();

            foreach ($items as $item) {
                $status  = ($item->getData('status') ? 'Enabled' : 'Disabled');
                $warning = !$item->getData('status');

                if (array_key_exists($item->getData('id'), $invalidated)) {
                    $status = 'Invalidated';
                    $warning = true;
                }

                $this->cacheTypes[$item->getData('cache_type')] = [
                    'value'   => $status,
                    'warning' => $warning,
                ];
            }
        }

        return $this->cacheTypes;
    }
}
