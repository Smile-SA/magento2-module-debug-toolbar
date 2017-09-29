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
use Smile\DebugToolbar\Helper\Observer as HelperObserver;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Observer extends AbstractZone
{
    /**
     * @var HelperObserver
     */
    protected $helperObserver;

    /**
     * Generic constructor.
     *
     * @param Context        $context
     * @param HelperData     $helperData
     * @param HelperObserver $helperObserver
     * @param array          $data
     */
    public function __construct(
        Context        $context,
        HelperData     $helperData,
        HelperObserver $helperObserver,
        array          $data = []
    ) {
        parent::__construct($context, $helperData, $data);

        $this->helperObserver  = $helperObserver;
    }

    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'observer';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Observer';
    }

    /**
     * Get the observer stats
     *
     * @return array
     */
    public function getObserverStats()
    {
        return $this->helperObserver->getEventStats();
    }

    /**
     * Prepare observers for display in the table
     *
     * @param array $observers
     *
     * @return string
     */
    public function buildHtmlInfo(array $observers = [])
    {
        $html = "
<table>
    <thead>
        <tr>
            <th>Observer</th>
            <th>Instance</th>
            <th>Disabled</th>
            <th>Nb Call</th>
            <th>Time Total</th>
            <th>Time Mean</th>
        </tr>    
    </thead>
    <tbody>";
        foreach ($observers as $observer) {
            $total = $this->displayHumanTimeMs($observer['time_total']);
            $mean = $this->displayHumanTimeMs($observer['time_mean']);

            $html.= "
        <tr>
            <td>".$this->escapeHtml($observer['observer_name'])."</td>
            <td>".$this->escapeHtml($observer['instance'])."</td>
            <td class=\"st-value-center\"  style=\"width: 100px;\">".($observer['disabled'] ? 'Yes' : 'No')."</td>
            <td class=\"st-value-number\"  style=\"width: 100px;\">".$this->escapeHtml($observer['nb_call'])."</td>
            <td class=\"st-value-unit-ms\" style=\"width: 100px;\">".$total."</td>
            <td class=\"st-value-unit-ms\" style=\"width: 100px;\">".$mean."</td>
        </tr>
            ";
        }

        $html.= "
    </tbody>
</table>";

        return $html;
    }
}
