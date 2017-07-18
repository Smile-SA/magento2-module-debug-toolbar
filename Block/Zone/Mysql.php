<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Context;
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
     * Get the html of the zone
     *
     * @return string
     */
    public function getZoneHtml()
    {
        $html = '';

        $list = $this->getQueries();

        foreach ($list as $key => $row) {
            $row['time'] = $this->displayHumanTimeMs($row['time']);
            $list[$key] = $row;
        }

        $html.= $this->displayTable(
            'Show All Queries',
            $list,
            [
                'id'    => [
                    'title' => 'Id',
                    'class' => 'st-value-number',
                ],
                'type'  => [
                    'title' => 'Type',
                    'class' => 'st-value-center',
                ],
                'time'  => [
                    'title' => 'Time',
                    'class' => 'st-value-unit-ms',
                ],
                'query' => [
                    'title' => 'Query',
                    'class' => '',
                ],
            ],
            [
                'params' => 'Params',
                'trace'  => 'Trace',
            ]
        );

        $sections = [
            'Number' => $this->getCountPerTypes(),
            'Time'   => $this->getTimePerTypes(),
            'Server' => [
                'Version' => $this->getMysqlVersion(),
            ],
        ];

        foreach ($sections['Time'] as $key => $value) {
            $sections['Time'][$key] = $this->displayHumanTime($value);
        }

        $sections['Number']['total'] = [
            'value'   => $sections['Number']['total'],
            'warning' => $sections['Number']['total'] > 200,
        ];

        $sections['Number']['connect'] = [
            'value'   => $sections['Number']['connect'],
            'warning' => $sections['Number']['connect'] > 1,
        ];

        $this->addToSummary('Mysql', 'Number', $sections['Number']['total']);
        $this->addToSummary('Mysql', 'Time', $sections['Time']['total']);

        $html.= $this->displaySections($sections);

        return $html;
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
