<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
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
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var string[]
     */
    protected $version;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get the connection.
     *
     * @return AdapterInterface
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
     * @param string $key
     * @return string
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
     * @return array
     * @throws Exception
     */
    public function getExecutedQueries(): array
    {
        return $this->getProfiler()->getQueryProfilesAsArray();
    }

    /**
     * Get the count per types.
     *
     * @return array
     * @throws Exception
     */
    public function getCountPerTypes(): array
    {
        return $this->getProfiler()->getCountPerTypes();
    }

    /**
     * Get the time per types.
     *
     * @return array
     * @throws Exception
     */
    public function getTimePerTypes(): array
    {
        return $this->getProfiler()->getTimePerTypes();
    }

    /**
     * Get the profiler.
     *
     * @return Profiler
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
}
