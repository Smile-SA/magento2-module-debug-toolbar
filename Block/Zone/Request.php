<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\App\Request\Http as MagentoRequest;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Request extends AbstractZone
{
    /**
     * @var MagentoRequest
     */
    protected $request;

    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'request';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Request';
    }

    /**
     * Get the html of the zone
     *
     * @return string
     */
    public function getZoneHtml()
    {
        $info = $this->getRequest();

        $sections = [
            'HTTP' => [
                'Version' => $info->getVersion(),
                'Scheme'  => $info->getScheme(),
                'Method'  => $info->getMethod(),
                'IP'      => $info->getClientIp(),
                'URL'     => $info->getUriString(),
            ],
            'Action' => [
                'Path Info'   => $info->getPathInfo(),
                'Full Action' => $info->getFullActionName(),
                'Module'      => $info->getModuleName(),
                'Group'       => $info->getControllerName(),
                'Action'      => $info->getActionName(),
            ],
            'Route' => [
                'Module'     => $info->getControllerModule(),
                'Route Name' => $info->getRouteName(),
                'Front Name' => $info->getFrontName(),
                'Controller' => $this->getControllerClassName(),
            ],
            'User Params' => (array) $info->getUserParams(),
            'Get'         => (array) $info->getQuery(),
            'Post'        => (array) $info->getPost(),
            'Files'       => (array) $info->getFiles(),
            'Env'         => array_merge((array) $info->getEnv(), (array) $info->getServer()),
            'Headers'     => [],
            'Cookies'     => $_COOKIE,
        ];

        foreach ($info->getHeaders() as $header) {
            $sections['Headers'][$header->getFieldName()] = $header->getFieldValue();
        }

        $sections['Headers']['Cookie'] = 'See in Cookies section';

        $this->addToSummary('Request', 'Url', $sections['HTTP']['URL']);
        $this->addToSummary('Request', 'Method', $sections['HTTP']['Method']);
        $this->addToSummary('Request', 'Path Info', $sections['Action']['Path Info']);
        $this->addToSummary('Request', 'Full Action', $sections['Action']['Full Action']);

        return $this->displaySections($sections);
    }

    /**
     * Set the request
     *
     * @param MagentoRequest $request
     *
     * @return $this
     */
    public function setRequest(MagentoRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the request
     *
     * @return MagentoRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the controller class name
     * @return string
     */
    public function getControllerClassName()
    {
        return $this->helperData->getValue('controller_classname');
    }
}
