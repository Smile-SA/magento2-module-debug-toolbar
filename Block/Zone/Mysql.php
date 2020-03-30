<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as HelperData;
use Smile\DebugToolbar\Model\ResourceModel\Info as ResourceModel;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Mysql extends AbstractZone
{
    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param FormatterFactory $formatterFactory
     * @param ResourceModel $resourceModel
     * @param array $data
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
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'mysql';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Mysql';
    }

    /**
     * Get all the queries.
     *
     * @return array
     */
    public function getQueries(): array
    {
        return $this->resourceModel->getExecutedQueries();
    }

    /**
     * Get count per types.
     *
     * @return array
     */
    public function getCountPerTypes(): array
    {
        return $this->resourceModel->getCountPerTypes();
    }

    /**
     * Get time per types.
     *
     * @return array
     */
    public function getTimePerTypes(): array
    {
        return $this->resourceModel->getTimePerTypes();
    }

    /**
     * Get the Mysql version.
     *
     * @return string
     */
    public function getMysqlVersion(): string
    {
        return $this->resourceModel->getMysqlVersion();
    }

    /**
     * Prepare params and trace for display in the table.
     *
     * @param array $params
     * @param array $trace
     * @return string
     */
    public function buildHtmlInfo(array $params = [], array $trace = []): string
    {
        $html = '';

        if (count($params) > 0) {
            $html .= "<h2>Query Parameters</h2>";
            $html .= "<table>";
            $html .= "<col style='width: 100px'/>";
            $html .= "<col style=''/>";
            $html .= "<thead><tr><th>Id</th><th>Value</th></tr></thead>";
            $html .= "<tbody>";
            foreach ($params as $key => $value) {
                $html .= "<tr>";
                $html .= "<th style=\"width: 30%\">" . $this->escapeHtml($key) . "</th>";
                $html .= "<td>" . $this->escapeHtml($value) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody>";
            $html .= "</table>";
            $html .= "<br>";
        }

        $html .= "<h2>PHP Trace</h2>";
        $html .= "<table>";
        $html .= "<col style='width: 40%'/>";
        $html .= "<col style='width: 80px'/>";
        $html .= "<col style='width: 50%'/>";
        $html .= "<thead><tr><th>File</th><th>Line</th><th>Code</th></tr></thead>";
        $html .= "<tbody>";
        foreach ($trace as $row) {
            $file = $row;
            $line = null;
            $code = null;
            if (preg_match('/^([^\(]+)\(([0-9]+)\): (.*)$/', $row, $match)) {
                $file = $match[1];
                $line = $match[2];
                $code = $match[3];
            }
            $file = str_replace(BP . '/', '', $file);
            $html .= "<tr>";
            $html .= "<td>" . $this->escapeHtml($file) . "</td>";
            $html .= "<td>" . $this->escapeHtml($line) . "</td>";
            $html .= "<td>" . $this->escapeHtml($code) . "</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        return $html;
    }
}
