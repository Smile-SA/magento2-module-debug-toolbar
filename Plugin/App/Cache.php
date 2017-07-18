<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Plugin\App;

use Magento\Framework\App\CacheInterface as MagentoCache;
use Smile\DebugToolbar\Helper\Cache as HelperCache;

/**
 * Plugin on Cache
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Cache
{
    /**
     * @var HelperCache
     */
    protected $helperCache;

    /**
     * Cache constructor.
     *
     * @param HelperCache $helperCache
     */
    public function __construct(
        HelperCache $helperCache
    ) {
        $this->helperCache = $helperCache;
    }

    /**
     * Add stats on load
     *
     * @param MagentoCache $subject
     * @param \Closure     $closure
     * @param string       $identifier
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(
        MagentoCache $subject,
        \Closure $closure,
        $identifier
    ) {
        $startTime = microtime(true);

        $result = $closure($identifier);

        $this->helperCache->addStat('load', $identifier, microtime(true) - $startTime, strlen($result));

        return $result;
    }

    /**
     * Add stats on save
     *
     * @param MagentoCache $subject
     * @param \Closure     $closure
     * @param string       $data
     * @param string       $identifier
     * @param array        $tags
     * @param int          $lifeTime
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        MagentoCache $subject,
        \Closure $closure,
        $data,
        $identifier,
        $tags = [],
        $lifeTime = null
    ) {
        $startTime = microtime(true);

        $result = $closure($data, $identifier, $tags, $lifeTime);

        $this->helperCache->addStat('save', $identifier, microtime(true) - $startTime, strlen($data));

        return $result;
    }

    /**
     * Add stats on remove
     *
     * @param MagentoCache $subject
     * @param \Closure     $closure
     * @param string       $identifier
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRemove(
        MagentoCache $subject,
        \Closure $closure,
        $identifier
    ) {
        $startTime = microtime(true);

        $result = $closure($identifier);

        $this->helperCache->addStat('remove', $identifier, microtime(true) - $startTime);

        return $result;
    }
}
