<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Formatter;

use Magento\Framework\Escaper;
use RuntimeException;

/**
 * Value formatter.
 *
 * @SuppressWarnings(PMD.ExcessiveClassComplexity)
 */
class Formatter
{
    /**
     * Max string length for values.
     */
    private const MAX_STRING_LENGTH = 200;

    protected mixed $value;
    protected string $formattedValue;
    protected string $type;
    protected ?string $subType = null;
    protected bool $warning = false;
    protected array $rules;
    protected array $cssClasses = [];

    public function __construct(protected Escaper $escaper, mixed $value, ?string $type = null, array $rules = [])
    {
        $this->prepareValueAndType($value, $type);
        $this->computeRules($rules);
        $this->computeFormattedValue();
        $this->computeCssClasses();
    }

    /**
     * Prepare the value and type.
     */
    protected function prepareValueAndType(mixed $value, ?string $type): void
    {
        if (is_object($value) || is_array($value)) {
            $type = 'printable';
            $value = print_r($value, true);
        }

        if ($value === null) {
            $value = 'NULL';
        }

        if ($value === true) {
            $value = 'TRUE';
        }

        if ($value === false) {
            $value = 'FALSE';
        }

        $this->value = $value;

        $this->prepareType($type);
    }

    /**
     * Prepare the type.
     */
    protected function prepareType(?string $type): void
    {
        if ($type === null) {
            $type = $this->getTypeFromValue();
        }

        $type = strtolower($type);

        $subType = null;
        if (strpos($type, '_') !== false) {
            [$type, $subType] = explode('_', $type, 2);
        }

        if ($type !== 'code' && mb_strlen((string) $this->value) > self::MAX_STRING_LENGTH) {
            $type = 'printable';
        }

        $this->type = $type;
        $this->subType = $subType;
    }

    /**
     * Get the type from the value.
     */
    protected function getTypeFromValue(): string
    {
        $this->value = (string) $this->value;

        if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $this->value, $match)) {
            return 'number';
        }

        if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->value, $match)) {
            return 'date';
        }

        if (preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $this->value, $match)) {
            return 'hour';
        }

        if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $this->value, $match)) {
            return 'datetime';
        }

        return 'text';
    }

    /**
     * Compute the rules.
     *
     * @throws RuntimeException
     */
    protected function computeRules(array $rules): bool
    {
        $this->rules = $rules;

        $this->warning = false;
        if (count($this->rules) === 0) {
            return false;
        }

        $this->warning = true;
        foreach ($this->rules as $ruleTest => $ruleValue) {
            if (!$this->computeRule($ruleTest, $ruleValue)) {
                $this->warning = false;
            }
        }

        return true;
    }

    /**
     * Compute a rule.
     *
     * @throws RuntimeException
     */
    protected function computeRule(string $ruleTest, mixed $ruleValue): bool
    {
        switch ($ruleTest) {
            case 'lt':
                return ($this->value < $ruleValue);

            case 'gt':
                return ($this->value > $ruleValue);

            case 'eq':
                return ($this->value === $ruleValue);

            case 'neq':
                return ($this->value !== $ruleValue);

            case 'in':
                return in_array($this->value, $ruleValue);

            case 'nin':
                return !in_array($this->value, $ruleValue);
        }

        throw new RuntimeException('Unknown Formatter Rule Test [' . $ruleTest . ']');
    }

    /**
     * Compute the formatted value.
     */
    protected function computeFormattedValue(): void
    {
        $value = $this->escaper->escapeHtml($this->value);

        switch ($this->type) {
            case 'code':
                $value = '<pre><code class="' . $this->subType . '">' . $value . '</code></pre>';
                break;

            case 'printable':
                $value = '<pre class="complex-value">' . $value . '</pre>';
                break;

            case 'size':
                $value = $this->displayHumanSize((int) $value);
                break;

            case 'time':
                $value = $this->displayHumanTime((float) $value);
                break;

            // text, hour, date, datetime, center, number
            default:
                break;
        }

        $this->formattedValue = $value;
    }

    /**
     * Display human size.
     */
    protected function displayHumanSize(int $value): string
    {
        if ($this->subType === 'ko') {
            $value /= (1024.);
        }

        if ($this->subType === 'mo') {
            $value /= 1024. * 1024.;
        }

        if ($this->subType === 'go') {
            $value /= (1024. * 1024. * 1024.);
        }

        if ($this->subType === null) {
            $this->subType = 'o';

            if ($value > 1024) {
                $value /= 1024.;
                $this->subType = 'ko';
            }

            if ($value > 1024) {
                $value /= 1024.;
                $this->subType = 'mo';
            }

            if ($value > 1024) {
                $value /= 1024.;
                $this->subType = 'go';
            }
        }

        return number_format($value, 3, '.', '');
    }

    /**
     * Display human time (in seconds).
     */
    protected function displayHumanTime(float $value): string
    {
        if ($this->subType === 'ms') {
            $value *= (1000.);
        }

        if ($this->subType === 'm') {
            $value /= (60.);
        }

        if ($this->subType === 'h') {
            $value /= (3600.);
        }

        if ($this->subType === null) {
            $this->subType = 's';

            if ($value > 120) {
                $value /= 60.;
                $this->subType = 'm';
            }

            if ($value > 120) {
                $value /= 60.;
                $this->subType = 'h';
            }

            if ($value < 1) {
                $value *= 1000.;
                $this->subType = 'ms';
            }
        }

        return number_format($value, 3, '.', '');
    }

    /**
     * Compute the css classes.
     */
    protected function computeCssClasses(): void
    {
        $css = 'st-value-' . $this->type;
        if ($this->subType) {
            $css .= '-' . $this->subType;
        }

        if ($this->type === 'code') {
            $css = 'hjs-code';
        }

        $this->cssClasses[] = $css;

        if ($this->warning) {
            $this->cssClasses[] = 'value-warning';
        }
    }

    /**
     * Get the formatted value.
     */
    public function getResult(): array
    {
        return [
            'value' => $this->formattedValue,
            'css_class' => implode(' ', $this->cssClasses),
            'has_warning' => $this->warning,
        ];
    }

    /**
     * Do we have a warning?
     */
    public function hasWarning(): bool
    {
        return $this->warning;
    }
}
