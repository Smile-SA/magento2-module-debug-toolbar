<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\App\Response\Http as ResponseHttp;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Response extends AbstractZone
{
    /**
     * @var ResponseHttp
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
     * Get the html of the zone
     *
     * @return string
     */
    public function getZoneHtml()
    {
        $response = $this->getResponse();

        $length = mb_strlen($response->getContent());

        $headers = [];
        foreach ($response->getHeaders() as $header) {
            $headers[$header->getFieldName()] = $header->getFieldValue();
        }

        $tags = [];
        if (array_key_exists('X-Magento-Tags', $headers)) {
            $tags = explode(',', $headers['X-Magento-Tags']);
            $headers['X-Magento-Tags'] = 'see in Full Page Cache section';
            sort($tags);
        }

        $esi = $this->getEsiUrlList();

        $sections = [
            'HTTP' => [
                'Version'       => $response->getVersion(),
                'Cookie'        => $response->getCookie(),
                'Response Code' => [
                    'value'   => $response->getHttpResponseCode(),
                    'warning' => $response->getHttpResponseCode() >= 400
                ],
                'Reason Phrase' => $response->getReasonPhrase(),
                'Status Code'   => [
                    'value'   => $response->getStatusCode(),
                    'warning' => $response->getStatusCode() >= 400
                ],
            ],
            'Headers' => $headers,
            'Response' => [
                'Date' => date('Y-m-d H:i:s'),
                'Size' => [
                    'value'   => $this->displayHumanSize($length),
                    'warning' => $length > 512*1024,
                ],
            ],
            'Full Page Cache' => [
                'Mode'    => [
                    'value'   => $this->getFullPageCacheMode(),
                    'warning' => $this->getFullPageCacheMode() !== 'varnish',
                ],
                'Nb Tags' => [
                    'value' => count($tags),
                    'warning' => count($tags) > 50,
                ],
                'Nb ESI'  => [
                    'value' => count($esi),
                    'warning' => count($esi) > 4,
                ],
                'Tags'    => $tags,
                'ESI'     => $esi,
            ],
        ];

        $this->addToSummary('Response', 'Date', $sections['Response']['Date']);
        $this->addToSummary('Response', 'Size', $sections['Response']['Size']);
        $this->addToSummary('Response', 'FPC Mode', $sections['Full Page Cache']['Mode']);
        $this->addToSummary('Response', 'FPC Tags', $sections['Full Page Cache']['Nb Tags']);
        $this->addToSummary('Response', 'ESI Tags', $sections['Full Page Cache']['Nb ESI']);

        return $this->displaySections($sections);
    }

    /**
     * Set the response
     *
     * @param ResponseHttp $response
     *
     * @return $this
     */
    public function setResponse(ResponseHttp $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get the Response
     *
     * @return ResponseHttp
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
