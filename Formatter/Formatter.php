<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Formatter;

use Magento\Framework\Escaper;

/**
 * Formatter.
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 * @SuppressWarnings(PMD.ExcessiveClassComplexity)
 */
class Formatter
{
    /**
     * Max string length for values.
     */
    const MAX_STRING_LENGTH = 200;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $formatedValue;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $subType;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var bool
     */
    protected $warning = false;

    /**
     * @var string
     */
    protected $cssClasses = [];

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @param Escaper $escaper
     * @param mixed $value
     * @param string|null $type
     * @param array $rules
     */
    public function __construct(Escaper $escaper, $value, $type = null, $rules = [])
    {
        $this->escaper = $escaper;

        $this->prepareValueAndType($value, $type);
        $this->computeRules($rules);
        $this->computeFormattedValue();
        $this->computeCssClasses();
    }

    /**
     * Prepare the value and type.
     *
     * @param mixed $value
     * @param string $type
     */
    protected function prepareValueAndType($value, $type)
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
     *
     * @param string|null $type
     */
    protected function prepareType($type)
    {
        if ($type === null) {
            $type = $this->getTypeFromValue();
        }

        $type = strtolower($type);

        $subType = null;
        if (strpos($type, '_') !== false) {
            list($type, $subType) = explode('_', $type, 2);
        }

        if ($type !== 'code' && mb_strlen((string) $this->value) > self::MAX_STRING_LENGTH) {
            $type = 'printable';
        }

        $this->type = $type;
        $this->subType = $subType;
    }

    /**
     * Get the type from the value.
     *
     * @return string
     */
    protected function getTypeFromValue()
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
     * @param array $rules
     * @return bool
     * @throws \Exception
     */
    protected function computeRules($rules)
    {
        $this->rules = $rules;

        $this->warning = false;
        if (count($this->rules) == 0) {
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
     * @param string $ruleTest
     * @param mixed $ruleValue
     * @return bool
     * @throws \Exception
     */
    protected function computeRule($ruleTest, $ruleValue)
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

        throw new \Exception('Unknown Formatter Rule Test [' . $ruleTest . ']');
    }

    /**
     * Compute the formatted value.
     */
    protected function computeFormattedValue()
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
                $value = $this->displayHumanSize($value);
                break;

            case 'time':
                $value = $this->displayHumanTime($value);
                break;

            // text, hour, date, datetime, center, number
            default:
                break;
        }

        $this->formatedValue = $value;
    }

    /**
     * Display human size.
     *
     * @param int $value
     * @return string
     */
    protected function displayHumanSize($value)
    {
        if ($this->subType === 'ko') {
            $value /= (1024.);
        }

        if ($this->subType === 'mo') {
            $value /= (1024. * 1024.);
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
     *
     * @param int $value
     * @return string
     */
    protected function displayHumanTime($value)
    {
        if ($this->subType == 'ms') {
            $value *= (1000.);
        }

        if ($this->subType == 'm') {
            $value /= (60.);
        }

        if ($this->subType == 'h') {
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
    protected function computeCssClasses()
    {
        $css = 'st-value-' . $this->type;
        if ($this->subType) {
            $css .= '-' . $this->subType;
        }

        if ($this->type == 'code') {
            $css = 'hjs-code';
        }

        $this->cssClasses[] = $css;

        if ($this->warning) {
            $this->cssClasses[] = 'value-warning';
        }
    }

    /**
     * Get the formatted value.
     *
     * @return array
     */
    public function getResult()
    {
        return [
            'value' => $this->formatedValue,
            'css_class' => implode(' ', $this->cssClasses),
            'has_warning' => $this->warning,
        ];
    }

    /**
     * Do we have a warning?
     *
     * @return bool
     */
    public function hasWarning()
    {
        return $this->warning;
    }
}
