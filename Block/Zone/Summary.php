<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

/**
 * Summary section.
 */
class Summary extends AbstractZone
{
    protected array $summary = [];

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
     */
    public function getSummarySections(): array
    {
        return $this->summary;
    }

    /**
     * @inheritdoc
     */
    public function addToSummary(string $sectionName, string $key, mixed $value): self
    {
        if (is_array($value) && array_key_exists('has_warning', $value) && $value['has_warning']) {
            $this->hasWarning();
        }

        $this->summary[$sectionName][$key] = $value;

        return $this;
    }
}
