<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\HTTP\PhpEnvironment\Response as MagentoResponse;

/**
 * Response section.
 */
class Response extends AbstractZone
{
    protected MagentoResponse $response;

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'response';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Response';
    }

    /**
     * Set the response.
     */
    public function setResponse(MagentoResponse $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get the response.
     */
    public function getResponse(): MagentoResponse
    {
        return $this->response;
    }

    /**
     * Get the Full Page Cache mode.
     */
    public function getFullPageCacheMode(): string
    {
        return $this->dataHelper->getFullPageCacheMode();
    }

    /**
     * Get the list of the ESI blocks in the response.
     *
     * @return string[]
     */
    public function getEsiUrlList(): array
    {
        $list = [];

        $pattern = '/<esi:include[\s]+[^>]*src=[\'"]([^\'"]+)[\'"][^>]*>/';
        if (preg_match_all($pattern, (string) $this->response->getContent(), $matches)) {
            $list = $matches[1];
        }

        return $list;
    }
}
