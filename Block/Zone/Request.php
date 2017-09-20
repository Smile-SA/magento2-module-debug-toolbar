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
