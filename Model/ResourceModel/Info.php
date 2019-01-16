<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Smile\DebugToolbar\DB\Profiler;

/**
 * Main Debug Toolbar Resource Model
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {
        return $this->resourceConnection->getConnection('read');
    }

    /**
     * Get Mysql versions.
     *
     * @return string[]
     */
    public function getMysqlVersions()
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
     */
    public function getMysqlVersion($key = 'version')
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
     */
    public function getExecutedQueries()
    {
        return $this->getProfiler()->getQueryProfilesAsArray();
    }

    /**
     * Get the count per types.
     *
     * @return array
     */
    public function getCountPerTypes()
    {
        return $this->getProfiler()->getCountPerTypes();
    }

    /**
     * Get the time per types.
     *
     * @return array
     */
    public function getTimePerTypes()
    {
        return $this->getProfiler()->getTimePerTypes();
    }

    /**
     * Get the profiler.
     *
     * @return \Smile\DebugToolbar\DB\Profiler
     * @throws \Exception
     */
    protected function getProfiler()
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $this->resourceConnection->getConnection();

        /** @var Profiler $profiler */
        $profiler = $connection->getProfiler();

        if (!($profiler instanceof Profiler)) {
            throw new \Exception(
                'DB Profiler is not set to \Smile\DebugToolbar\DB\Profiler. Please disable and enable the ToolBar'
            );
        }

        return $profiler;
    }
}
