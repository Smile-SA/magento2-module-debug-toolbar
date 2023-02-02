<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\DB;

use Zend_Db_Profiler as OriginalProfiler;

/**
 * DB profiler.
 */
class Profiler extends OriginalProfiler
{
    /**
     * @inheritDoc
     */
    public function queryStart($queryText, $queryType = null): ?int
    {
        if (!$this->_enabled) {
            return null;
        }

        // Make sure we have a query type
        if (null === $queryType) {
            $queryType = $this->getTypeFromQuery($queryText);
        }

        $this->_queryProfiles[] = new Profiler\Query($queryText, $queryType);
        end($this->_queryProfiles);

        return key($this->_queryProfiles);
    }

    /**
     * Get the query type.
     */
    protected function getTypeFromQuery(string $queryText): int
    {
        $queryText = ltrim($queryText);

        $base = substr($queryText, 0, 6);
        if ($queryText[0] === '(') {
            $base = substr($queryText, 1, 6);
        }
        $base = strtolower($base);

        switch ($base) {
            case 'insert':
                $queryType = self::INSERT;
                break;
            case 'update':
                $queryType = self::UPDATE;
                break;
            case 'delete':
                $queryType = self::DELETE;
                break;
            case 'select':
                $queryType = self::SELECT;
                break;
            default:
                $queryType = self::QUERY;
                break;
        }

        return $queryType;
    }
}
