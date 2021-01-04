<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\HTTP\PhpEnvironment\Response as MagentoResponse;

/**
 * Response section.
 */
class Response extends AbstractZone
{
    /**
     * @var MagentoResponse
     */
    protected $response;

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
     *
     * @param MagentoResponse $response
     * @return $this
     */
    public function setResponse(MagentoResponse $response): Response
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get the response.
     *
     * @return MagentoResponse
     */
    public function getResponse(): MagentoResponse
    {
        return $this->response;
    }

    /**
     * Get the Full Page Cache mode.
     *
     * @return string
     */
    public function getFullPageCacheMode(): string
    {
        return $this->helperData->getFullPageCacheMode();
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
        if (preg_match_all($pattern, $this->response->getContent(), $matches)) {
            $list = $matches[1];
        }

        return $list;
    }
}
