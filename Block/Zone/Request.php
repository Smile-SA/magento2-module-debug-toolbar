<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\App\Request\Http as MagentoRequest;
use Magento\Framework\App\RequestInterface;

/**
 * Request section.
 */
class Request extends AbstractZone
{
    protected MagentoRequest $request;

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'request';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Request';
    }

    /**
     * Set the request.
     */
    public function setRequest(MagentoRequest $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Get the controller class name.
     */
    public function getControllerClassName(): string
    {
        return (string) $this->dataHelper->getValue('controller_classname');
    }
}
