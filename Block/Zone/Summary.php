<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

/**
 * Summary Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <dirtech@smile.fr>
 * @copyright 2018 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Summary extends AbstractZone
{
    /**
     * @var array
     */
    protected $summary = [];

    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'summary';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Summary';
    }

    /**
     * get the summary sections
     *
     * @return array
     */
    public function getSummarySections()
    {
        return $this->summary;
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
        if (is_array($value) && array_key_exists('has_warning', $value) && $value['has_warning']) {
            $this->hasWarning();
        }

        $this->summary[$sectionName][$key] = $value;
    }
}
