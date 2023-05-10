<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as DataHelper;

/**
 * Abstract zone block.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
abstract class AbstractZone extends Template
{
    protected Summary $summaryBlock;
    protected bool $warning = false;
    protected array $tablesToDisplay = [];

    public function __construct(
        Context $context,
        protected DataHelper $dataHelper,
        protected FormatterFactory $formatterFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setData('cache_lifetime', 0);
        $this->setTemplate('Smile_DebugToolbar::zone/' . $this->getCode() . '.phtml');
    }

    /**
     * Get the code.
     */
    abstract public function getCode(): string;

    /**
     * Get the title.
     */
    abstract public function getTitle(): string;

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        return $this->_toHtml();
    }

    /**
     * Add the summary block.
     */
    public function setSummaryBlock(Summary $block): self
    {
        $this->summaryBlock = $block;

        return $this;
    }

    /**
     * Add a value to the summary.
     */
    public function addToSummary(string $sectionName, string $key, mixed $value): void
    {
        $this->summaryBlock->addToSummary($sectionName, $key, $value);
    }

    /**
     * Set the warning flag.
     */
    public function hasWarning(): self
    {
        $this->warning = true;

        return $this;
    }

    /**
     * Have we a warning?
     */
    public function isWarning(): bool
    {
        return $this->warning;
    }

    /**
     * Display sections.
     */
    public function displaySections(array $sections = []): string
    {
        $html = '';

        foreach ($sections as $sectionName => $sectionValues) {
            $html .= "<h2>$sectionName</h2>\n";

            $html .= "<table>\n";
            $html .= '<tbody>';
            if (count($sectionValues) === 0) {
                $html .= "<tr><td>No Values</td></tr>\n";
            }

            foreach ($sectionValues as $name => $value) {
                $html .= $this->displaySectionValue((string) $name, $value);
            }
            $html .= '</tbody>';

            $html .= "</table>\n";
        }

        return $html;
    }

    /**
     * Display sections.
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
     * @return string[]
     */
    protected function getClassAndValue(mixed $value): array
    {
        if (
            !is_array($value)
            || !array_key_exists('value', $value)
            || !array_key_exists('css_class', $value)
        ) {
            $value = $this->formatValue($value);
        }

        return [$value['css_class'], $value['value']];
    }

    /**
     * Display a row section.
     */
    protected function displaySectionValue(string $name, mixed $value): string
    {
        [$class, $value] = $this->getClassAndValue($value);

        return "    <tr><th>$name</th><td class=\"$class\">$value</td></tr>\n";
    }

    /**
     * Get the toolbar id.
     */
    public function getToolbarId(): string
    {
        return $this->dataHelper->getToolbarId();
    }

    /**
     * Display the table.
     */
    public function displayTable(string $title, array &$values, array $columns, ?string $additional = null): string
    {
        $tableId = $this->dataHelper->getNewTableId();
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
     */
    public function getTablesToDisplay(): array
    {
        return $this->tablesToDisplay;
    }

    /**
     * Get a timer.
     */
    public function getTimer(string $code): float
    {
        return $this->dataHelper->getTimer($code);
    }

    /**
     * Format a value, using rules, and type.
     */
    public function formatValue(mixed $value, array $rules = [], ?string $type = null): array
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
