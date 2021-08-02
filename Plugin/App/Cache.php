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
use Smile\DebugToolbar\Helper\Cache as CacheHelper;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;

/**
 * Fetch cache info.
 */
class Cache
{
    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var CacheHelper
     */
    protected $cacheHelper;

    /**
     * @param ConfigHelper $configHelper
     * @param CacheHelper $cacheHelper
     */
    public function __construct(ConfigHelper $configHelper, CacheHelper $cacheHelper)
    {
        $this->configHelper = $configHelper;
        $this->cacheHelper = $cacheHelper;
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
        if (!$this->configHelper->isEnabled()) {
            return $closure($identifier);
        }

        $startTime = microtime(true);
        $result = $closure($identifier);
        $this->cacheHelper->addStat(
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
        if (!$this->configHelper->isEnabled()) {
            return $closure($data, $identifier, $tags, $lifeTime);
        }

        $startTime = microtime(true);
        $result = $closure($data, $identifier, $tags, $lifeTime);
        $this->cacheHelper->addStat(
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
        if (!$this->configHelper->isEnabled()) {
            return $closure($identifier);
        }

        $startTime = microtime(true);
        $result = $closure($identifier);
        $this->cacheHelper->addStat(
            'remove',
            (string) $identifier,
            microtime(true) - $startTime
        );

        return $result;
    }
}
