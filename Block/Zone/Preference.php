<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as DataHelper;
use Smile\DebugToolbar\Helper\Preference as PreferenceHelper;

/**
 * Preference section.
 */
class Preference extends AbstractZone
{
    /**
     * @var PreferenceHelper
     */
    protected $preferenceHelper;

    /**
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param FormatterFactory $formatterFactory
     * @param PreferenceHelper $preferenceHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        FormatterFactory $formatterFactory,
        PreferenceHelper $preferenceHelper,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $formatterFactory, $data);
        $this->preferenceHelper = $preferenceHelper;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'preference';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Preference';
    }

    /**
     * Get the plugin stats.
     *
     * @return array
     */
    public function getPluginStats(): array
    {
        return $this->preferenceHelper->getPluginStats();
    }

    /**
     * Get the preference stats.
     *
     * @return array
     */
    public function getPreferenceStats(): array
    {
        return $this->preferenceHelper->getPreferenceStats();
    }

    /**
     * Get html info.
     *
     * @param string[] $methods
     * @return string
     */
    public function buildPluginHtmlInfo(array $methods): string
    {
        $html = "
<table>
    <colgroup>
        <col style='width: 80%' />
        <col style='width: 20%' />
    </colgroup>
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
