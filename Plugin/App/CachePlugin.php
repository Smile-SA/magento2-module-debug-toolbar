<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin\App;

use Closure;
use Magento\Framework\App\CacheInterface;
use Smile\DebugToolbar\Helper\Cache as CacheHelper;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;

/**
 * Fetch cache info.
 */
class CachePlugin
{
    protected ConfigHelper $configHelper;
    protected CacheHelper $cacheHelper;

    public function __construct(ConfigHelper $configHelper, CacheHelper $cacheHelper)
    {
        $this->configHelper = $configHelper;
        $this->cacheHelper = $cacheHelper;
    }

    /**
     * Add stats on load.
     *
     * @return string|bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(CacheInterface $subject, Closure $closure, string $identifier)
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        CacheInterface $subject,
        Closure $closure,
        string $data,
        string $identifier,
        array $tags = [],
        ?int $lifeTime = null
    ): bool {
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRemove(CacheInterface $subject, Closure $closure, string $identifier): bool
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
