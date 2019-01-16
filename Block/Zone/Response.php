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
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
    public function getCode()
    {
        return 'response';
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Response';
    }

    /**
     * Set the response.
     *
     * @param MagentoResponse $response
     * @return $this
     */
    public function setResponse(MagentoResponse $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get the response.
     *
     * @return MagentoResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the Full Page Cache mode.
     *
     * @return string
     */
    public function getFullPageCacheMode()
    {
        return $this->helperData->getFullPageCacheMode();
    }

    /**
     * Get the list of the ESI blocks in the response.
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
