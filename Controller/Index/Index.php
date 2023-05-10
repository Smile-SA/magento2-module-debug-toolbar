<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Index implements HttpGetActionInterface
{
    public function __construct(protected ResultFactory $resultFactory)
    {
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
