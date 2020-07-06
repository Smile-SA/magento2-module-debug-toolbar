<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template as MagentoTemplateBlock;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Zone for Debug Toolbar Block
 *
 * @api
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
abstract class AbstractZone extends MagentoTemplateBlock
{
    /**
     * @var bool
     */
    protected $warning = false;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var FormatterFactory
     */
    protected $formatterFactory;

    /**
     * @var Summary
     */
    protected $summaryBlock;

    /**
     * @var array
     */
    protected $tablesToDisplay = [];

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param FormatterFactory $formatterFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        FormatterFactory $formatterFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helperData = $helperData;
        $this->formatterFactory = $formatterFactory;

        $this->setData('cache_lifetime', 0);
        $this->setTemplate('Smile_DebugToolbar::zone/' . $this->getCode() . '.phtml');
    }

    /**
     * Get the code.
     *
     * @return string
     */
    abstract public function getCode(): string;

    /**
     * Get the title.
     *
     * @return string
     */
    abstract public function getTitle(): string;

    /**
     * @inheritdoc
     */
    public function toHtml(): string
    {
        return $this->_toHtml();
    }

    /**
     * Add the summary block.
     *
     * @param Summary $block
     *
     * @return $this
     */
    public function setSummaryBlock(Summary $block): AbstractZone
    {
        $this->summaryBlock = $block;

        return $this;
    }

    /**
     * Add a value to the summary.
     *
     * @param string $sectionName
     * @param string $key
     * @param mixed $value
     */
    public function addToSummary(string $sectionName, string $key, $value): void
    {
        $this->summaryBlock->addToSummary($sectionName, $key, $value);
    }

    /**
     * Set the warning flag.
     *
     * @return $this
     */
    public function hasWarning(): AbstractZone
    {
        $this->warning = true;

        return $this;
    }

    /**
     * Have we a warning?
     *
     * @return bool
     */
    public function isWarning(): bool
    {
        return $this->warning;
    }

    /**
     * Display sections.
     *
     * @param array $sections
     * @return string
     */
    public function displaySections(array $sections = []): string
    {
        $html = '';

        foreach ($sections as $sectionName => $sectionValues) {
            $html .= "<h2>{$sectionName}</h2>\n";

            $html .= "<table>\n";
            $html .= '<tbody>';
            if (count($sectionValues) === 0) {
                $html .= "<tr><td>No Values</td></tr>\n";
            }

            foreach ($sectionValues as $name => $value) {
                $html .= $this->displaySectionValue($name, $value);
            }
            $html .= '</tbody>';

            $html .= "</table>\n";
        }

        return $html;
    }

    /**
     * Display sections.
     *
     * @param array $sections
     * @return string
     */
    public function displaySectionsGrouped(array $sections = []): string
    {
        $sectionNames = array_keys($sections);
        $rowNames = array_keys($sections[$sectionNames[0]]);

        $html = "<table>\n";
        $html .= '<thead><tr><th></th><td>' . implode('</td><td>', $sectionNames) . '</td></tr></thead>';
        $html .= '<tbody>';
        foreach ($rowNames as $rowName) {
            $html .= '<tr>';
            $html .= '<th>' . $rowName . '</th>';
            foreach ($sectionNames as $sectionName) {
                [$class, $value] = $this->getClassAndValue($sections[$sectionName][$rowName]);
                $html .= '<td class="' . $class . '">' . $value . '</td>';
            }
            $html .= '<tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    /**
     * Get the good css class and the cleaned value.
     *
     * @param mixed $value
     * @return string[]
     */
    protected function getClassAndValue($value): array
    {
        if (!is_array($value)
            || !array_key_exists('value', $value)
            || !array_key_exists('css_class', $value)
        ) {
            $value = $this->formatValue($value);
        }

        return [$value['css_class'], $value['value']];
    }

    /**
     * Display a row section.
     *
     * @param string|int $name
     * @param mixed $value
     * @return string
     */
    protected function displaySectionValue($name, $value): string
    {
        [$class, $value] = $this->getClassAndValue($value);

        return "    <tr><th>{$name}</th><td class=\"{$class}\">{$value}</td></tr>\n";
    }

    /**
     * Get the toolbar id.
     *
     * @return string
     */
    public function getToolbarId(): string
    {
        return $this->helperData->getToolbarId();
    }

    /**
     * Display the table.
     *
     * @param string $title
     * @param array $values
     * @param array $columns
     * @param string|null $additional
     * @return string
     */
    public function displayTable(string $title, array &$values, array $columns, ?string $additional = null): string
    {
        $tableId = $this->helperData->getNewTableId();
        $tableTitle = str_replace('-', '_', $tableId) . '_title';
        $tableValues = str_replace('-', '_', $tableId) . '_values';
        $tableColumns = str_replace('-', '_', $tableId) . '_columns';
        $tableAdditional = str_replace('-', '_', $tableId) . '_additional';

        $html = '<script type="text/javascript">' . "\n";
        $html .= 'var ' . $tableTitle . ' = ' . json_encode(strip_tags($title)) . ';' . "\n";
        $html .= 'var ' . $tableColumns . ' = ' . json_encode($columns) . ';' . "\n";
        $html .= 'var ' . $tableValues . ' = ' . json_encode($values) . ';' . "\n";
        $html .= 'var ' . $tableAdditional . ' = ' . json_encode($additional) . ';' . "\n";
        $html .= "</script>\n";

        $label = 'Show ' . $title . ' (' . count($values) . ' rows)';

        $onClick = 'smileToolbarTableDisplay('
            . $tableTitle . ', '
            . $tableValues . ', '
            . $tableColumns . ', '
            . $tableAdditional . ');';

        $this->tablesToDisplay[] = [
            'label' => $label,
            'onclick' => $onClick,
        ];

        return $html;
    }

    /**
     * Get the tables to display.
     *
     * @return array
     */
    public function getTablesToDisplay(): array
    {
        return $this->tablesToDisplay;
    }

    /**
     * Get the data helper.
     *
     * @return HelperData
     */
    public function getHelperData(): HelperData
    {
        return $this->helperData;
    }

    /**
     * Format a value, using rules, and type.
     *
     * @param string|float $value
     * @param array $rules
     * @param string|null $type
     *
     * @return array
     */
    public function formatValue($value, array $rules = [], ?string $type = null): array
    {
        $formatter = $this->formatterFactory->create(
            [
                'value' => $value,
                'type' => $type,
                'rules' => $rules,
            ]
        );

        if ($formatter->hasWarning()) {
            $this->hasWarning();
        }

        return $formatter->getResult();
    }
}
