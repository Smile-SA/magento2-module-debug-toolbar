<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

/**
 * Summary Zone for Debug Toolbar Block
 *
 * @api
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Summary extends AbstractZone
{
    /**
     * @var array
     */
    protected $summary = [];

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'summary';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Summary';
    }

    /**
     * Get the summary sections.
     *
     * @return array
     */
    public function getSummarySections(): array
    {
        return $this->summary;
    }

    /**
     * @inheritdoc
     */
    public function addToSummary(string $sectionName, string $key, $value): void
    {
        if (is_array($value) && array_key_exists('has_warning', $value) && $value['has_warning']) {
            $this->hasWarning();
        }

        $this->summary[$sectionName][$key] = $value;
    }
}
