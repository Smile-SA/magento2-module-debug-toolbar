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
use Smile\DebugToolbar\Helper\Observer as HelperObserver;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Observer extends AbstractZone
{
    /**
     * @var HelperObserver
     */
    protected $helperObserver;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param FormatterFactory $formatterFactory
     * @param HelperObserver $helperObserver
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        FormatterFactory $formatterFactory,
        HelperObserver $helperObserver,
        array $data = []
    ) {
        parent::__construct($context, $helperData, $formatterFactory, $data);

        $this->helperObserver = $helperObserver;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'observer';
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Observer';
    }

    /**
     * Get the observer stats.
     *
     * @return array
     */
    public function getObserverStats()
    {
        return $this->helperObserver->getEventStats();
    }

    /**
     * Prepare observers for display in the table.
     *
     * @param array $observers
     * @return string
     */
    public function buildHtmlInfo(array $observers = [])
    {
        $html = '<table>';

        // Table head
        $html .= '<thead><tr>';
        $html .= '<th>Observer</th><th>Instance</th><th>Disabled</th>';
        $html .= '<th>Nb Call</th><th>Time Total</th><th>Time Mean</th>';
        $html .= '</tr></thead>';

        // Table body
        $html .= '<body>';

        foreach ($observers as $observer) {
            $name = $this->formatValue($observer['observer_name'], [], 'text');
            $instance = $this->formatValue($observer['instance'], [], 'text');
            $disabled = $this->formatValue(($observer['disabled'] ? 'Yes' : 'No'), [], 'center');
            $call = $this->formatValue($observer['nb_call'], [], 'number');
            $total = $this->formatValue($observer['time_total'], [], 'time_ms');
            $mean = $this->formatValue($observer['time_mean'], [], 'time_ms');

            $html .= '<tr>';
            $html .= '<td class="' . $name['css_class'] . '">' . $name['value'] . '</td>';
            $html .= '<td class="' . $instance['css_class'] . '">' . $instance['value'] . '</td>';
            $html .= '<td class="' . $disabled['css_class'] . '" style="width: 100px;">' . $disabled['value'] . '</td>';
            $html .= '<td class="' . $call['css_class'] . '" style="width: 100px;">' . $call['value'] . '</td>';
            $html .= '<td class="' . $total['css_class'] . '" style="width: 120px;">' . $total['value'] . '</td>';
            $html .= '<td class="' . $mean['css_class'] . '" style="width: 120px;">' . $mean['value'] . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }
}
