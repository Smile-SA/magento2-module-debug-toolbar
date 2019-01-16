<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\DB;

use Zend_Db_Profiler as OriginalProfiler;

/**
 * Smile Db Profiler
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Profiler extends OriginalProfiler
{
    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string|null
     */
    protected $lastQueryId;

    /**
     * @var array
     */
    protected $queries;

    /**
     * @var string[]
     */
    protected $types = [
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
     *
     * @param string $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Setter for database connection type.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Starts a query. Creates a new query profile object.
     *
     * @param string $queryText
     * @param int|null $queryType
     * @return int|null
     */
    public function queryStart($queryText, $queryType = null)
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
     *
     * @param string $queryText
     * @return int
     */
    protected function getTypeFromQuery($queryText)
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
     *
     * @param int $typeId
     * @return string
     */
    protected function getTypeText($typeId)
    {
        $type = 'query';

        if (array_key_exists($typeId, $this->types)) {
            $type = $this->types[$typeId];
        }

        return $type;
    }

    /**
     * Ends a query. Pass it the handle that was returned by queryStart().
     *
     * @param int $queryId
     * @return string
     */
    public function queryEnd($queryId)
    {
        $this->lastQueryId = null;

        return parent::queryEnd($queryId);
    }

    /**
     * Ends the last query if exists. Used for finalize broken queries.
     *
     * @return string
     */
    public function queryEndLast()
    {
        if ($this->lastQueryId !== null) {
            return $this->queryEnd($this->lastQueryId);
        }

        return self::IGNORED;
    }

    /**
     * Get the queries as array.
     *
     * @return array
     */
    public function getQueryProfilesAsArray()
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
     *
     * @param Profiler\Query $queryProfile
     * @return array
     */
    protected function convertQueryProfileToArray(Profiler\Query $queryProfile)
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
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Get a count per types.
     *
     * @return array
     */
    public function getCountPerTypes()
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
     *
     * @return array
     */
    public function getTimePerTypes()
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
