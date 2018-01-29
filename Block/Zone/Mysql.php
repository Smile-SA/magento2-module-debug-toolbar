<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Helper\Data  as HelperData;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Model\ResourceModel\Info as ResourceModel;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <dirtech@smile.fr>
 * @copyright 2018 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
     * @param Context          $context
     * @param HelperData       $helperData
     * @param FormatterFactory $formatterFactory
     * @param ResourceModel    $resourceModel
     * @param array            $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        FormatterFactory $formatterFactory,
        ResourceModel $resourceModel,
        array $data = []
    ) {
        parent::__construct($context, $helperData, $formatterFactory, $data);

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

    /**
     * Prepare params and trace for display in the table
     *
     * @param array $params
     * @param array $trace
     *
     * @return string
     */
    public function buildHtmlInfo(array $params = [], array $trace = [])
    {
        $html = '';

        if (count($params)>0) {
            $html.= "<h2>Query Parameters</h2>";
            $html.= "<table>";
            $html.= "<col style='width: 100px'/>";
            $html.= "<col style=''/>";
            $html.= "<thead><tr><th>Id</th><th>Value</th></tr></thead>";
            $html.= "<tbody>";
            foreach ($params as $key => $value) {
                $html.= "<tr>";
                $html.= "<th style=\"width: 30%\">".$this->escapeHtml($key)."</th>";
                $html.= "<td>".$this->escapeHtml($value)."</td>";
                $html.= "</tr>";
            }
            $html.= "</tbody>";
            $html.= "</table>";
            $html.= "<br />";
        }

        $html.= "<h2>PHP Trace</h2>";
        $html.= "<table>";
        $html.= "<col style='width: 40%'/>";
        $html.= "<col style='width: 80px'/>";
        $html.= "<col style='width: 50%'/>";
        $html.= "<thead><tr><th>File</th><th>Line</th><th>Code</th></tr></thead>";
        $html.= "<tbody>";
        foreach ($trace as $row) {
            $file = $row;
            $line = null;
            $code = null;
            if (preg_match('/^([^\(]+)\(([0-9]+)\): (.*)$/', $row, $match)) {
                $file = $match[1];
                $line = $match[2];
                $code = $match[3];
            }
            $file = str_replace(BP.'/', '', $file);
            $html.= "<tr>";
            $html.= "<td>".$this->escapeHtml($file)."</td>";
            $html.= "<td>".$this->escapeHtml($line)."</td>";
            $html.= "<td>".$this->escapeHtml($code)."</td>";
            $html.= "</tr>";
        }
        $html.= "</table>";
        return $html;
    }
}
