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
use Smile\DebugToolbar\Helper\Preference as HelperPreference;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Preference extends AbstractZone
{
    /**
     * @var HelperPreference
     */
    protected $helperPreference;

    /**
     * Generic constructor.
     *
     * @param Context          $context
     * @param HelperData       $helperData
     * @param HelperPreference $helperPreference
     * @param array            $data
     */
    public function __construct(
        Context          $context,
        HelperData       $helperData,
        HelperPreference $helperPreference,
        array            $data = []
    ) {
        parent::__construct($context, $helperData, $data);

        $this->helperPreference  = $helperPreference;
    }

    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'preference';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Preference';
    }

    /**
     * Get the plugin stats
     *
     * @return array
     */
    public function getPluginStats()
    {
        return $this->helperPreference->getPluginStats();
    }

    /**
     * Get the preference stats
     *
     * @return array
     */
    public function getPreferenceStats()
    {
        return $this->helperPreference->getPreferenceStats();
    }

    /**
     * Get html info
     *
     * @param string[] $methods
     *
     * @return string
     */
    public function buildPluginHtmlInfo($methods)
    {
        $html = "
<table>
    <col style='width: 80%' />
    <col style='width: 20%' />
    <thead>
        <tr>
            <th>Method</th>
            <th>Type</th>
        </tr>    
    </thead>
    <tbody>";
        foreach ($methods as $method => $type) {
            $html.= "
        <tr>
            <td class=\"\">".$method."</td>
            <td class=\"st-value-center\">".$type."</td>
        </tr>
            ";
        }

        $html.= "
    </tbody>
</table>";

        return $html;
    }
}
