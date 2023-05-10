<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Model\ResourceModel;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use RuntimeException;
use Smile\DebugToolbar\DB\Profiler;
use Zend_Db_Statement_Exception;

/**
 * Main debug toolbar resource model.
 */
class Info
{
    /**
     * @var string[]|null
     */
    protected ?array $version = null;
    /**
     * @var string[]
     */
    protected array $queryTypes = [
        'connect',
        'query',
        'insert',
        'update',
        'delete',
        'select',
        'transaction',
    ];

    public function __construct(protected ResourceConnection $resourceConnection)
    {
    }

    /**
     * Get the connection.
     */
    public function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection('read');
    }

    /**
     * Get Mysql versions.
     *
     * @return string[]
     * @throws Zend_Db_Statement_Exception
     */
    public function getMysqlVersions(): array
    {
        if ($this->version === null) {
            $this->version = [];

            //@SmileAnalyserSkip magento2/mysql
            $values = $this->getConnection()->query('SHOW VARIABLES LIKE "%version%"')->fetchAll();
            foreach ($values as $value) {
                $this->version[$value['Variable_name']] = $value['Value'];
            }
        }

        return $this->version;
    }

    /**
     * Get Mysql version.
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function getMysqlVersion(string $key = 'version'): string
    {
        $values = $this->getMysqlVersions();

        if (!array_key_exists($key, $values)) {
            return '';
        }

        return $values[$key];
    }

    /**
     * Get the executed queries.
     *
     * @throws Exception
     */
    public function getExecutedQueries(): array
    {
        return $this->getProfiler()->getQueryProfiles();
    }

    /**
     * Get the count per types.
     *
     * @throws Exception
     */
    public function getCountPerTypes(): array
    {
        $list = [
            'total' => 0,
        ];

        foreach ($this->getQueryTypes() as $value) {
            $list[$value] = 0;
        }

        foreach ($this->getProfiler()->getQueryProfiles() as $query) {
            $list['total']++;
            $list[$query->getTypeAsString()]++;
        }

        return $list;
    }

    /**
     * Get the time per types.
     *
     * @throws Exception
     */
    public function getTimePerTypes(): array
    {
        $list = [
            'total' => 0,
        ];

        foreach ($this->getQueryTypes() as $value) {
            $list[$value] = 0;
        }

        foreach ($this->getProfiler()->getQueryProfiles() as $query) {
            $list['total'] += $query->getElapsedSecs();
            $list[$query->getTypeAsString()] += $query->getElapsedSecs();
        }

        return $list;
    }

    /**
     * Get the profiler.
     *
     * @throws RuntimeException
     */
    protected function getProfiler(): Profiler
    {
        /** @var Mysql $connection */
        $connection = $this->resourceConnection->getConnection();

        /** @var Profiler $profiler */
        $profiler = $connection->getProfiler();

        if (!($profiler instanceof Profiler)) {
            throw new RuntimeException(
                'DB Profiler is not set to \Smile\DebugToolbar\DB\Profiler. Please disable and enable the ToolBar'
            );
        }

        return $profiler;
    }

    /**
     * Get the list of query types.
     *
     * @return string[]
     */
    protected function getQueryTypes(): array
    {
        return $this->queryTypes;
    }
}
