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
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
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
     * Get the html of the zone
     *
     * @return string
     */
    public function getZoneHtml()
    {
        return $this->displaySections($this->getSummarySections());
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
        $this->summary[$sectionName][$key] = $value;
    }
}
