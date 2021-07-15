<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin\App;

use Closure;
use Magento\Framework\App\CacheInterface;
use Smile\DebugToolbar\Helper\Cache as HelperCache;
use Smile\DebugToolbar\Helper\Config as HelperConfig;

/**
 * Fetch cache info.
 */
class Cache
{
    /**
     * @var HelperConfig
     */
    protected $helperConfig;

    /**
     * @var HelperCache
     */
    protected $helperCache;

    /**
     * @param HelperConfig $helperConfig
     * @param HelperCache $helperCache
     */
    public function __construct(HelperConfig $helperConfig, HelperCache $helperCache)
    {
        $this->helperConfig = $helperConfig;
        $this->helperCache = $helperCache;
    }

    /**
     * Add stats on load.
     *
     * @param CacheInterface $subject
     * @param Closure $closure
     * @param string $identifier
     * @return string|bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(CacheInterface $subject, Closure $closure, $identifier)
    {
        if (!$this->helperConfig->isEnabled()) {
            return $closure($identifier);
        }

        $startTime = microtime(true);
        $result = $closure($identifier);
        $this->helperCache->addStat(
            'load',
            (string) $identifier,
            microtime(true) - $startTime,
            strlen((string) $result)
        );

        return $result;
    }

    /**
     * Add stats on save.
     *
     * @param CacheInterface $subject
     * @param Closure $closure
     * @param string $data
     * @param string $identifier
     * @param array $tags
     * @param int|null $lifeTime
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        CacheInterface $subject,
        Closure $closure,
        $data,
        $identifier,
        $tags = [],
        $lifeTime = null
    ) {
        if (!$this->helperConfig->isEnabled()) {
            return $closure($data, $identifier, $tags, $lifeTime);
        }

        $startTime = microtime(true);
        $result = $closure($data, $identifier, $tags, $lifeTime);
        $this->helperCache->addStat(
            'save',
            (string) $identifier,
            microtime(true) - $startTime,
            strlen((string) $data)
        );

        return $result;
    }

    /**
     * Add stats on remove.
     *
     * @param CacheInterface $subject
     * @param Closure $closure
     * @param string $identifier
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRemove(CacheInterface $subject, Closure $closure, $identifier)
    {
        if (!$this->helperConfig->isEnabled()) {
            return $closure($identifier);
        }

        $startTime = microtime(true);
        $result = $closure($identifier);
        $this->helperCache->addStat(
            'remove',
            (string) $identifier,
            microtime(true) - $startTime
        );

        return $result;
    }
}
