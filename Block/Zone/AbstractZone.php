<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template as MagentoTemplateBlock;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Helper\Data  as HelperData;
use Smile\DebugToolbar\Formatter\FormatterFactory;

/**
 * Zone for Debug Toolbar Block
 *
 * @api
 * @author    Laurent MINGUET <dirtech@smile.fr>
 * @copyright 2018 Smile
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
     * AbstractZone constructor.
     *
     * @param Context          $context
     * @param HelperData       $helperData
     * @param FormatterFactory $formatterFactory
     * @param array            $data
     */
    public function __construct(
        Context          $context,
        HelperData       $helperData,
        FormatterFactory $formatterFactory,
        array            $data = []
    ) {
        parent::__construct($context, $data);

        $this->helperData       = $helperData;
        $this->formatterFactory = $formatterFactory;

        $this->setData('cache_lifetime', 0);
        $this->setTemplate('zone/'.$this->getCode().'.phtml');
    }

    /**
     * Get the code
     *
     * @return string
     */
    abstract public function getCode();

    /**
     * Get the title
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Redefine the toHtml method to remove all the cache policy
     *
     * @return string
     */
    public function toHtml()
    {
         return $this->_toHtml();
    }

    /**
     * Add the summary block
     *
     * @param Summary $block
     *
     * @return $this
     */
    public function setSummaryBlock(Summary $block)
    {
        $this->summaryBlock = $block;

        return $this;
    }

    /**
     * Add a value to the summary
     *
     * @param string $sectionName
     * @param string $key
     * @param mixed  $value
     */
    public function addToSummary($sectionName, $key, $value)
    {
        $this->summaryBlock->addToSummary($sectionName, $key, $value);
    }

    /**
     * Set the warning flag
     *
     * @return $this
     */
    public function hasWarning()
    {
        $this->warning = true;

        return $this;
    }

    /**
     * Have we a warning ?
     *
     * @return bool
     */
    public function isWarning()
    {
        return $this->warning;
    }

    /**
     * Display sections
     *
     * @param array $sections
     *
     * @return string
     */
    public function displaySections($sections = [])
    {
        $html = '';

        foreach ($sections as $sectionName => $sectionValues) {
            $html.= "<h2>{$sectionName}</h2>\n";

            $html.= "<table>\n";
            $html.= '<tbody>';
            if (count($sectionValues) == 0) {
                $html.= "<tr><td>No Values</td></tr>\n";
            }

            foreach ($sectionValues as $name => $value) {
                $html.= $this->displaySectionValue($name, $value);
            }
            $html.= '</tbody>';

            $html.= "</table>\n";
        }

        return $html;
    }

    /**
     * Display sections
     *
     * @param array $sections
     *
     * @return string
     */
    public function displaySectionsGrouped($sections = [])
    {
        $sectionNames = array_keys($sections);
        $rowNames = array_keys($sections[$sectionNames[0]]);

        $html = "<table>\n";
        $html.= '<thead><tr><th></th><td>'.implode('</td><td>', $sectionNames).'</td></tr></thead>';
        $html.= '<tbody>';
        foreach ($rowNames as $rowName) {
            $html.= '<tr>';
            $html.= '<th>'.$rowName.'</th>';
            foreach ($sectionNames as $sectionName) {
                list($class, $value) = $this->getClassAndValue($sections[$sectionName][$rowName]);
                $html.= '<td class="'.$class.'">'.$value.'</td>';
            }
            $html.= '<tr>';
        }
        $html.= '</tbody>';
        $html.= '</table>';

        return $html;
    }

    /**
     * Get the good css class and the cleaned value
     *
     * @param mixed $value
     *
     * @return string[]
     */
    protected function getClassAndValue($value)
    {
        if (!is_array($value)
            || !array_key_exists('value', $value)
            || !array_key_exists('css_class', $value)
        ) {
            $value = $this->formatValue($value);
        }

        return array($value['css_class'], $value['value']);
    }

    /**
     * Display a row section
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return string
     */
    protected function displaySectionValue($name, $value)
    {
        list($class, $value) = $this->getClassAndValue($value);

        return "    <tr><th>{$name}</th><td class=\"{$class}\">{$value}</td></tr>\n";
    }

    /**
     * Get the toolbar id
     *
     * @return string
     */
    public function getToolbarId()
    {
        return $this->helperData->getToolbarId();
    }

    /**
     * Display the table
     *
     * @param string $title
     * @param array  $values
     * @param array  $columns
     * @param string $additionnal
     *
     * @return string
     */
    public function displayTable($title, &$values, $columns, $additionnal = null)
    {
        $tableId = $this->helperData->getNewTableId();
        $tableTitle       = str_replace('-', '_', $tableId).'_title';
        $tableValues      = str_replace('-', '_', $tableId).'_values';
        $tableColumns     = str_replace('-', '_', $tableId).'_columns';
        $tableAdditionnal = str_replace('-', '_', $tableId).'_additionnal';

        $html = '<script type="text/javascript">'."\n";
        $html.= 'var '.$tableTitle.'       = '.json_encode(strip_tags($title)).';'."\n";
        $html.= 'var '.$tableColumns.'     = '.json_encode($columns).';'."\n";
        $html.= 'var '.$tableValues.'      = '.json_encode($values).';'."\n";
        $html.= 'var '.$tableAdditionnal.' = '.json_encode($additionnal).';'."\n";
        $html.= "</script>\n";

        $label   = 'Show '.$title.' ('.count($values).' rows)';

        $onClick = 'smileToolbarTableDisplay('
            .$tableTitle.', '
            .$tableValues.', '
            .$tableColumns.', '
            .$tableAdditionnal.');';

        $this->tablesToDisplay[] = [
            'label'   => $label,
            'onclick' => $onClick,
        ];

        return $html;
    }

    /**
     * get the tables to display
     *
     * @return array
     */
    public function getTablesToDisplay()
    {
        return $this->tablesToDisplay;
    }

    /**
     * get the helper data
     *
     * @return HelperData
     */
    public function getHelperData()
    {
        return $this->helperData;
    }

    /**
     * format a value, using rules, and type
     *
     * @param float  $value
     * @param array  $rules
     * @param string $type
     *
     * @return array
     */
    public function formatValue($value, $rules = [], $type = null)
    {
        $formatter = $this->formatterFactory->create(
            [
                'value' => $value,
                'type'  => $type,
                'rules' => $rules,
            ]
        );

        if ($formatter->hasWarning()) {
            $this->hasWarning();
        }

        return $formatter->getResult();
    }
}
