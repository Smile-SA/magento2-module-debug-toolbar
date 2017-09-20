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
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Zone for Debug Toolbar Block
 * We do not use phtml template files because we do not want to duplicate the templates between FO and BO
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
abstract class AbstractZone extends MagentoTemplateBlock
{
    /**
     * max string length for values
     */
    const MAX_STRING_LENGTH = 128;

    /**
     * @var bool
     */
    protected $warning = false;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var Summary
     */
    protected $summaryBlock;

    /**
     * AbstractZone constructor.
     *
     * @param Context    $context
     * @param HelperData $helperData
     * @param array      $data
     */
    public function __construct(
        Context    $context,
        HelperData $helperData,
        array      $data = []
    ) {
        parent::__construct($context, $data);

        $this->helperData = $helperData;
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
     * Prepare Basic Value
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function prepareBasicValue($value)
    {
        if ($value === null) {
            $value = 'NULL';
        }

        if ($value === true) {
            $value = 'TRUE';
        }

        if ($value === false) {
            $value = 'FALSE';
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function escapeField($value)
    {
        $value = $this->prepareBasicValue($value);

        $printable = false;

        if (is_array($value) || is_object($value)) {
            $printable = true;
            $value = print_r($value, true);
        }

        if (mb_strlen($value) > self::MAX_STRING_LENGTH) {
            $printable = true;
        }

        $value = $this->escapeHtml($value);
        if ($printable) {
            $value = '<pre class="complex-value">'.$value.'</pre>';
        }

        return $value;
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
            if (count($sectionValues) == 0) {
                $html.= "<tr><td>No Values</td></tr>\n";
            }

            foreach ($sectionValues as $name => $value) {
                $html.= $this->displaySectionValue($name, $value);
            }

            $html.= "</table>\n";
        }

        return $html;
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
        $warning = false;
        $class = [];
        if (is_array($value) && array_key_exists('value', $value) && array_key_exists('warning', $value)) {
            if ($value['warning']) {
                $this->hasWarning();
                $warning = true;
            }
            $value = $value['value'];
        }
        $value = $this->escapeField($value);
        if ($warning) {
            $class[] = 'value-warning';
        }

        $classValue = $this->getClassFromType($value);
        if (!is_null($classValue)) {
            $class[] = $classValue;
        }

        $class = implode(' ', $class);

        return "    <tr><th class=\"{$class}\">{$name}</th><td class=\"{$class}\">{$value}</td></tr>\n";
    }

    /**
     * Get the css class from the value
     *
     * @param string $value
     *
     * @return string|null
     */
    protected function getClassFromType($value)
    {
        if (preg_match('/^[0-9\.]+ ([a-zA-Z]+)$/', $value, $match)) {
            return 'st-value-unit-'.strtolower($match[1]);
        }

        if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $value, $match)) {
            return 'st-value-number';
        }

        if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value, $match)) {
            return 'st-value-date';
        }

        if (preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value, $match)) {
            return 'st-value-time';
        }

        if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value, $match)) {
            return 'st-value-datetime';
        }

        return null;
    }

    /**
     * Display a human size int
     *
     * @param int $value
     *
     * @return string
     */
    public function displayHumanSize($value)
    {
        return $this->helperData->displayHumanSize($value);
    }

    /**
     * Display a human size int in KO
     *
     * @param int $value
     *
     * @return string
     */
    public function displayHumanSizeKo($value)
    {
        return $this->helperData->displayHumanSizeKo($value);
    }

    /**
     * Display a human time int
     *
     * @param int $value
     *
     * @return string
     */
    public function displayHumanTime($value)
    {
        return $this->helperData->displayHumanTime($value);
    }

    /**
     * Display a human time int
     *
     * @param int $value
     *
     * @return string
     */
    public function displayHumanTimeMs($value)
    {
        return $this->helperData->displayHumanTimeMs($value);
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
     * @param array  $additionnal
     *
     * @return string
     */
    public function displayTable($title, &$values, $columns, $additionnal = [])
    {
        $html = '';

        $tableId = $this->helperData->getNewTableId();
        $tableValues      = str_replace('-', '_', $tableId).'_values';
        $tableColumns     = str_replace('-', '_', $tableId).'_columns';
        $tableAdditionnal = str_replace('-', '_', $tableId).'_additionnal';

        $html.= "<br />\n";
        $html.= '<script type="text/javascript">'."\n";
        $html.= 'var '.$tableColumns.' = '.json_encode($columns).';'."\n";
        $html.= 'var '.$tableValues.' = '.json_encode($values).';'."\n";
        $html.= 'var '.$tableAdditionnal.' = '.json_encode($additionnal).';'."\n";
        $html.= "</script>\n";
        $html.= '<a onclick="smileToolbarTableDisplay('.$tableValues.', '.$tableColumns.', '.$tableAdditionnal.');">';
        $html.= $title.' ('.count($values).' rows)';
        $html.= "</a>\n";
        $html.= "<br />\n";
        $html.= "<br />\n";

        return $html;
    }
}
