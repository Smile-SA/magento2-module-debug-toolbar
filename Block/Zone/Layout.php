<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as DataHelper;
use Smile\DebugToolbar\Helper\Layout as LayoutHelper;

/**
 * Layout section.
 */
class Layout extends AbstractZone
{
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        FormatterFactory $formatterFactory,
        protected LayoutHelper $layoutHelper,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $formatterFactory, $data);
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'layout';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Layout';
    }

    /**
     * Get the layout build.
     */
    public function getLayoutBuild(): array
    {
        return $this->layoutHelper->getLayoutBuild();
    }

    /**
     * Get the updated handles.
     */
    public function getHandles(): array
    {
        return $this->layoutHelper->getHandles();
    }

    /**
     * Display the layout table.
     */
    public function displayLayoutTable(): string
    {
        return $this->displayLayoutRecursive($this->getLayoutBuild(), 0);
    }

    /**
     * Display a recursive table.
     */
    protected function displayLayoutRecursive(array $list, int $level): string
    {
        $html = '';
        foreach ($list as $row) {
            $html .= $this->displayLayoutRecursiveRow($row, $level);
            $html .= $this->displayLayoutRecursive($row['children'], $level + 1);
        }

        return $html;
    }

    /**
     * Display a row of a recursive table.
     */
    protected function displayLayoutRecursiveRow(array $row, int $level): string
    {
        $prefix = $this->getToolbarId() . '-lt-';

        $span = '<span class="st-expand">&nbsp;</span>';
        if ($row['nb_child'] > 0) {
            $span = '<span ';
            $span .= ' class="st-expand"';
            $span .= ' id="' . $prefix . $row['name'] . '-span"';
            $span .= ' onclick="smileToolbarTreeGrid(this)"';
            $span .= '>+</span>';
        }

        $html = '<tr';
        $html .= ' id="' . $prefix . $row['name'] . '"';
        $html .= ' class="' . ($row['parent'] ? $prefix . $row['parent'] : '') . '"';
        $html .= ' style="' . ($row['parent'] ? 'display: none' : '') . '"';
        $html .= '>';
        $html .= '<td style="padding-left: ' . (10 * $level) . 'px" ';
        $html .= 'class="' . ($row['nb_child'] > 0 ? 'st-with-children' : '') . '">' . $span . $row['name'] . '</td>';
        $html .= '<td>';
        $html .= '<pre class="complex-value" style="width: 370px;">';
        $html .= 'Type:         ' . $row['type'] . "\n";
        $html .= 'ScopePrivate: ' . ($row['scope'] ? 'Yes' : 'No') . "\n";
        $html .= 'Cacheable:    ' . ($row['cacheable'] ? 'Yes' : 'No') . "\n";
        $html .= 'Class:        ' . $row['classname'] . "\n";
        $html .= 'Template:     ' . $row['template'] . "\n";
        $html .= '</pre>';
        $html .= '</td>';
        $html .= '</tr>';

        return $html . "\n";
    }
}
