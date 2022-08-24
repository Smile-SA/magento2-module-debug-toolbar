<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as DataHelper;
use Smile\DebugToolbar\Model\ResourceModel\Info as ResourceModel;

/**
 * MySQL section.
 */
class Mysql extends AbstractZone
{
    protected ResourceModel $resourceModel;
    protected DirectoryList $directoryList;

    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        FormatterFactory $formatterFactory,
        ResourceModel $resourceModel,
        DirectoryList $directoryList,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $formatterFactory, $data);
        $this->resourceModel = $resourceModel;
        $this->directoryList = $directoryList;
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
     */
    public function getQueries(): array
    {
        return $this->resourceModel->getExecutedQueries();
    }

    /**
     * Get count per types.
     */
    public function getCountPerTypes(): array
    {
        return $this->resourceModel->getCountPerTypes();
    }

    /**
     * Get time per types.
     */
    public function getTimePerTypes(): array
    {
        return $this->resourceModel->getTimePerTypes();
    }

    /**
     * Get the Mysql version.
     */
    public function getMysqlVersion(): string
    {
        return $this->resourceModel->getMysqlVersion();
    }

    /**
     * Prepare params and trace for display in the table.
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
                $html .= "<th style=\"width: 30%\">" . $this->_escaper->escapeHtml($key) . "</th>";
                $html .= "<td>" . $this->_escaper->escapeHtml($value) . "</td>";
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
            $file = str_replace($this->directoryList->getRoot() . '/', '', $file);
            $html .= "<tr>";
            $html .= "<td>" . $this->_escaper->escapeHtml($file) . "</td>";
            $html .= "<td>" . $this->_escaper->escapeHtml($line) . "</td>";
            $html .= "<td>" . $this->_escaper->escapeHtml($code) . "</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        return $html;
    }
}
