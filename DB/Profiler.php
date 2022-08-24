<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\DB;

use Zend_Db_Profiler as OriginalProfiler;

/**
 * DB profiler.
 */
class Profiler extends OriginalProfiler
{
    protected ?array $queries = null;
    protected string $host = '';
    protected string $type = '';
    /** @var string|int|null $lastQueryId */
    protected $lastQueryId = null;

    /**
     * @var string[]
     */
    protected array $types = [
        self::CONNECT => 'connect',
        self::QUERY => 'query',
        self::INSERT => 'insert',
        self::UPDATE => 'update',
        self::DELETE => 'delete',
        self::SELECT => 'select',
        self::TRANSACTION => 'transaction',
    ];

    /**
     * Setter for host IP.
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Setter for database connection type.
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

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

        $this->lastQueryId = key($this->_queryProfiles);

        return $this->lastQueryId;
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

    /**
     * Get a type from its id.
     */
    protected function getTypeText(int $typeId): string
    {
        $type = 'query';

        if (array_key_exists($typeId, $this->types)) {
            $type = $this->types[$typeId];
        }

        return $type;
    }

    /**
     * @inheritDoc
     */
    public function queryEnd($queryId): string
    {
        $this->lastQueryId = null;

        return parent::queryEnd($queryId);
    }

    /**
     * Get the queries as array.
     */
    public function getQueryProfilesAsArray(): array
    {
        if ($this->queries === null) {
            $this->queries = [];

            $queryProfiles = $this->getQueryProfiles();
            if (is_array($queryProfiles)) {
                foreach ($queryProfiles as $queryProfile) {
                    $query = $this->convertQueryProfileToArray($queryProfile);
                    $query['id'] = count($this->queries);

                    $this->queries[] = $query;
                }
            }
        }

        return $this->queries;
    }

    /**
     * Convert a query profile to a array.
     */
    protected function convertQueryProfileToArray(Profiler\Query $queryProfile): array
    {
        return [
            'id' => null,
            'query' => $queryProfile->getQuery(),
            'type_id' => $queryProfile->getQueryType(),
            'type' => $this->getTypeText($queryProfile->getQueryType()),
            'time' => $queryProfile->getElapsedSecs(),
            'params' => $queryProfile->getQueryParams(),
            'trace' => $queryProfile->getTrace(),
        ];
    }

    /**
     * Get the list of all the available types.
     *
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Get a count per types.
     */
    public function getCountPerTypes(): array
    {
        $list = [
            'total' => 0,
        ];

        foreach ($this->getTypes() as $value) {
            $list[$value] = 0;
        }

        foreach ($this->getQueryProfilesAsArray() as $query) {
            $list['total']++;
            $list[$query['type']]++;
        }

        return $list;
    }

    /**
     * Get a count per types.
     */
    public function getTimePerTypes(): array
    {
        $list = [
            'total' => 0,
        ];

        foreach ($this->getTypes() as $value) {
            $list[$value] = 0;
        }

        foreach ($this->getQueryProfilesAsArray() as $query) {
            $list['total'] += $query['time'];
            $list[$query['type']] += $query['time'];
        }

        return $list;
    }
}
