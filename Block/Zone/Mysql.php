<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Helper\Data as HelperData;
use Smile\DebugToolbar\Model\ResourceModel\Info as ResourceModel;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Mysql extends AbstractZone
{

    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * Mysql constructor.
     *
     * @param Context       $context
     * @param HelperData    $helper
     * @param ResourceModel $resourceModel
     * @param array         $data
     */
    public function __construct(
        Context       $context,
        HelperData    $helper,
        ResourceModel $resourceModel,
        array         $data = []
    ) {
        parent::__construct($context, $helper, $data);

        $this->resourceModel = $resourceModel;
    }

    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'mysql';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Mysql';
    }

    /**
     * Get all the queries
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->resourceModel->getExecutedQueries();
    }

    /**
     * Get count per types
     *
     * @return array
     */
    public function getCountPerTypes()
    {
        return $this->resourceModel->getCountPerTypes();
    }

    /**
     * Get time per types
     *
     * @return array
     */
    public function getTimePerTypes()
    {
        return $this->resourceModel->getTimePerTypes();
    }

    /**
     * Get the Mysql Version
     *
     * @return string
     */
    public function getMysqlVersion()
    {
        return $this->resourceModel->getMysqlVersion();
    }
}