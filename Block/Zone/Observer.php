<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as DataHelper;
use Smile\DebugToolbar\Helper\Observer as ObserverHelper;

/**
 * Observer section.
 */
class Observer extends AbstractZone
{
    /**
     * @var ObserverHelper
     */
    protected $observerHelper;

    /**
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param FormatterFactory $formatterFactory
     * @param ObserverHelper $observerHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        FormatterFactory $formatterFactory,
        ObserverHelper   $observerHelper,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $formatterFactory, $data);
        $this->observerHelper = $observerHelper;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'observer';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Observer';
    }

    /**
     * Get the observer stats.
     *
     * @return array
     */
    public function getObserverStats(): array
    {
        return $this->observerHelper->getEventStats();
    }

    /**
     * Prepare observers for display in the table.
     *
     * @param array $observers
     * @return string
     */
    public function buildHtmlInfo(array $observers = []): string
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
