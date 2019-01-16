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
use Smile\DebugToolbar\Helper\Preference as HelperPreference;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Preference extends AbstractZone
{
    /**
     * @var HelperPreference
     */
    protected $helperPreference;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param FormatterFactory $formatterFactory
     * @param HelperPreference $helperPreference
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        FormatterFactory $formatterFactory,
        HelperPreference $helperPreference,
        array $data = []
    ) {
        parent::__construct($context, $helperData, $formatterFactory, $data);

        $this->helperPreference = $helperPreference;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'preference';
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Preference';
    }

    /**
     * Get the plugin stats.
     *
     * @return array
     */
    public function getPluginStats()
    {
        return $this->helperPreference->getPluginStats();
    }

    /**
     * Get the preference stats.
     *
     * @return array
     */
    public function getPreferenceStats()
    {
        return $this->helperPreference->getPreferenceStats();
    }

    /**
     * Get html info.
     *
     * @param string[] $methods
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
            <th class=\"st-value-center\">Type</th>
        </tr>    
    </thead>
    <tbody>";
        foreach ($methods as $method => $type) {
            $html .= "
        <tr>
            <td>" . $method . "</td>
            <td class=\"st-value-center\">" . $type . "</td>
        </tr>
            ";
        }

        $html .= "
    </tbody>
</table>";

        return $html;
    }
}
