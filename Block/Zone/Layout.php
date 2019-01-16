<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as HelperData;
use Smile\DebugToolbar\Helper\Layout as HelperLayout;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Layout extends AbstractZone
{
    /**
     * @var HelperLayout
     */
    protected $helperLayout;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param FormatterFactory $formatterFactory
     * @param HelperLayout $helperLayout
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        FormatterFactory $formatterFactory,
        HelperLayout $helperLayout,
        array $data = []
    ) {
        parent::__construct($context, $helperData, $formatterFactory, $data);

        $this->helperLayout = $helperLayout;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'layout';
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Layout';
    }

    /**
     * Get the layout build.
     *
     * @return array
     */
    public function getLayoutBuild()
    {
        return $this->helperLayout->getLayoutBuild();
    }

    /**
     * Get the updated handles.
     *
     * @return array
     */
    public function getHandles()
    {
        return $this->helperLayout->getHandles();
    }

    /**
     * Display the layout table.
     *
     * @return string
     */
    public function displayLayoutTable()
    {
        return $this->displayLayoutRecursive($this->getLayoutBuild(), 0);
    }

    /**
     * Display a recursive table.
     *
     * @param array $list
     * @param int $level
     * @return string
     */
    protected function displayLayoutRecursive($list, $level)
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
     *
     * @param array $row
     * @param int $level
     * @return string
     */
    protected function displayLayoutRecursiveRow($row, $level)
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
        $html .= 'class="' . (($row['nb_child'] > 0) ? 'st-with-children' : '') . '">' . $span . $row['name'] . '</td>';
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
