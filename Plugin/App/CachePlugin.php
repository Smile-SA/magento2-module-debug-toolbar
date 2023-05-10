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
    public function __construct(protected ConfigHelper $configHelper, protected CacheHelper $cacheHelper)
    {
    }

    /**
     * Add stats on load.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(CacheInterface $subject, Closure $closure, string $identifier): mixed
    {
        if (!$this->configHelper->isEnabled()) {
            return $closure($identifier);
        }

        $startTime = microtime(true);
        $result = $closure($identifier);
        $this->cacheHelper->addStat(
            'load',
            $identifier,
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
            $identifier,
            microtime(true) - $startTime,
            strlen($data)
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
            $identifier,
            microtime(true) - $startTime
        );

        return $result;
    }
}
