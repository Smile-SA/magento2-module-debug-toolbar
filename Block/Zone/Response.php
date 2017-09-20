<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\HTTP\PhpEnvironment\Response as MagentoResponse;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Response extends AbstractZone
{
    /**
     * @var MagentoResponse
     */
    protected $response;

    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'response';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Response';
    }

    /**
     * Set the response
     *
     * @param MagentoResponse $response
     *
     * @return $this
     */
    public function setResponse(MagentoResponse $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get the Response
     *
     * @return MagentoResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the Full Page Cache mode
     *
     * @return string
     */
    public function getFullPageCacheMode()
    {
        return $this->helperData->getFullPageCacheMode();
    }

    /**
     * Get the list of the ESI blocks in the response
     *
     * @return string[]
     */
    public function getEsiUrlList()
    {
        $list = [];

        $pattern = '/<esi:include[\s]+[^>]*src=[\'"]([^\'"]+)[\'"][^>]*>/';
        if (preg_match_all($pattern, $this->response->getContent(), $matches)) {
            $list = $matches[1];
        }

        return $list;
    }
}
