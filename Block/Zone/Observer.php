<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Helper\Data     as HelperData;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Observer as HelperObserver;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <dirtech@smile.fr>
 * @copyright 2018 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
     * @param Context          $context
     * @param HelperData       $helperData
     * @param FormatterFactory $formatterFactory
     * @param HelperObserver   $helperObserver
     * @param array            $data
     */
    public function __construct(
        Context          $context,
        HelperData       $helperData,
        FormatterFactory $formatterFactory,
        HelperObserver   $helperObserver,
        array            $data = []
    ) {
        parent::__construct($context, $helperData, $formatterFactory, $data);

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
            $row = [
                'name'     => $this->formatValue($observer['observer_name'], [], 'text'),
                'instance' => $this->formatValue($observer['instance'], [], 'text'),
                'disabled' => $this->formatValue(($observer['disabled'] ? 'Yes' : 'No'), [], 'center'),
                'nb_call'  => $this->formatValue($observer['nb_call'], [], 'number'),
                'total'    => $this->formatValue($observer['time_total'], [], 'time_ms'),
                'mean'     => $this->formatValue($observer['time_mean'], [], 'time_ms'),
            ];

            $html.= "
        <tr>
            <td class=\"".$row['name']['css_class']."\"     >".$row['name']['value']."</td>
            <td class=\"".$row['instance']['css_class']."\" >".$row['instance']['value']."</td>
            <td class=\"".$row['disabled']['css_class']."\" style=\"width: 100px;\">".$row['disabled']['value']."</td>
            <td class=\"".$row['nb_call']['css_class']."\"  style=\"width: 100px;\">".$row['nb_call']['value']."</td>
            <td class=\"".$row['total']['css_class']."\"    style=\"width: 120px;\">".$row['total']['value']."</td>
            <td class=\"".$row['mean']['css_class']."\"     style=\"width: 120px;\">".$row['mean']['value']."</td>
        </tr>
            ";
        }

        $html.= "
    </tbody>
</table>";

        return $html;
    }
}
